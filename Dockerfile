# =============================================================================
# Stage 1: Build assets (Node.js)
# =============================================================================
FROM node:22-alpine AS node-builder

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci --no-audit

COPY vite.config.js ./
COPY resources/ resources/
RUN npm run build

# =============================================================================
# Stage 2: PHP-FPM Application
# =============================================================================
FROM php:8.3-fpm-alpine AS app

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libzip-dev \
    oniguruma-dev \
    openssl-dev \
    unzip \
    zip \
    mysql-client \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install \
        bcmath \
        exif \
        gd \
        mbstring \
        opcache \
        pdo \
        pdo_mysql \
        zip \
    && rm -rf /var/cache/apk/*

# Install Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Create application user (non-root)
RUN addgroup -g 1000 www && adduser -u 1000 -G www -s /bin/sh -D www

# Set working directory
WORKDIR /var/www/html

# Copy Composer files and install PHP dependencies
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --no-scripts \
    --prefer-dist

# Copy application source code
COPY --chown=www:www . .

# Copy built frontend assets
COPY --from=node-builder --chown=www:www /app/public/build ./public/build

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# Create necessary directories and set permissions
RUN mkdir -p storage/app/public \
             storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache \
    && chown -R www:www storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Switch to non-root user
USER www

EXPOSE 9000

CMD ["php-fpm"]

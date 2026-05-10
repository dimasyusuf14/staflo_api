#!/usr/bin/env bash
# =============================================================================
# init-ssl.sh — Issue SSL certificates via Certbot + Cloudflare DNS challenge
#
# Usage: ./scripts/init-ssl.sh
# Run this ONCE on the VPS after the stack is up for the first time.
# =============================================================================
set -euo pipefail

# ── Load env ─────────────────────────────────────────────────────────────────
if [ ! -f .env ]; then
  echo "[ERROR] .env file not found. Copy .env.production to .env first."
  exit 1
fi

source .env

DOMAIN="${APP_DOMAIN:?APP_DOMAIN must be set in .env}"
EMAIL="${CERTBOT_EMAIL:?CERTBOT_EMAIL must be set in .env}"
CLOUDFLARE_INI="docker/certbot/cloudflare.ini"

# ── Validate cloudflare.ini ───────────────────────────────────────────────────
if [ ! -f "$CLOUDFLARE_INI" ]; then
  echo "[ERROR] $CLOUDFLARE_INI not found."
  echo "  Create it with:"
  echo "    dns_cloudflare_api_token = YOUR_CLOUDFLARE_API_TOKEN"
  exit 1
fi

chmod 600 "$CLOUDFLARE_INI"

echo "==> Requesting certificate for: $DOMAIN and www.$DOMAIN"

docker compose run --rm certbot certonly \
  --dns-cloudflare \
  --dns-cloudflare-credentials /etc/letsencrypt/cloudflare.ini \
  --dns-cloudflare-propagation-seconds 30 \
  --email "$EMAIL" \
  --agree-tos \
  --no-eff-email \
  --force-renewal \
  -d "$DOMAIN" \
  -d "www.$DOMAIN"

echo "==> Certificate issued successfully."
echo "==> Switching Nginx to HTTPS config..."

# Remove HTTP-only config and enable the HTTPS config
# The full app.conf (with SSL) references ${DOMAIN}, so substitute it
DOMAIN="$DOMAIN" envsubst '${DOMAIN}' \
  < docker/nginx/conf.d/app.conf \
  > docker/nginx/conf.d/app.conf.rendered

# Replace placeholder config with rendered one (backup first)
cp docker/nginx/conf.d/app.conf docker/nginx/conf.d/app.conf.bak
cp docker/nginx/conf.d/app.conf.rendered docker/nginx/conf.d/app.conf
rm docker/nginx/conf.d/app-http-only.conf 2>/dev/null || true

echo "==> Reloading Nginx..."
docker compose exec nginx nginx -t && docker compose exec nginx nginx -s reload

echo "==> SSL setup complete! https://$DOMAIN is now live."

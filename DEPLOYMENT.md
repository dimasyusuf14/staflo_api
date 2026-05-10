# Deployment Guide — Staflo API

Panduan lengkap untuk mendeploy **Staflo API** (Laravel 13) ke VPS menggunakan **Docker**, **Nginx**, **PHP-FPM 8.3**, **MySQL 8.4**, dan **SSL via Certbot + Cloudflare DNS**.

---

## Daftar Isi

1. [Arsitektur Stack](#arsitektur-stack)
2. [Prasyarat](#prasyarat)
3. [Persiapan Cloudflare](#persiapan-cloudflare)
4. [Persiapan VPS](#persiapan-vps)
5. [Clone & Konfigurasi](#clone--konfigurasi)
6. [Issue SSL Certificate (Pertama Kali)](#issue-ssl-certificate-pertama-kali)
7. [Deploy Aplikasi](#deploy-aplikasi)
8. [Update Aplikasi](#update-aplikasi)
9. [Renewal SSL Otomatis](#renewal-ssl-otomatis)
10. [Monitoring & Troubleshooting](#monitoring--troubleshooting)
11. [Struktur File Docker](#struktur-file-docker)

---

## Arsitektur Stack

```
Internet
   │
   ▼
Cloudflare (DNS + CDN Proxy)
   │  Port 443 (HTTPS)
   ▼
VPS / Server
   ├── Nginx (port 80/443)  ──→  Redirect HTTP → HTTPS
   │                        ──→  Proxy pass ke PHP-FPM
   ├── PHP-FPM 8.3 (port 9000)  ──→  Laravel App
   ├── MySQL 8.4                ──→  Database
   ├── Queue Worker             ──→  Jobs (database driver)
   └── Certbot (Cloudflare DNS) ──→  Let's Encrypt SSL
```

---

## Prasyarat

### Di VPS
- OS: Ubuntu 22.04 / 24.04 LTS (direkomendasikan)
- RAM: minimal 1 GB (2 GB direkomendasikan)
- Docker Engine >= 24.x
- Docker Compose >= 2.x
- Git
- Port 80 dan 443 terbuka di firewall

### Install Docker di VPS (Ubuntu)

```bash
# Install dependencies
sudo apt update && sudo apt install -y ca-certificates curl gnupg

# Add Docker GPG key
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg \
  | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

# Add Docker repository
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
  https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" \
  | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Install Docker
sudo apt update && sudo apt install -y docker-ce docker-ce-cli containerd.io \
  docker-buildx-plugin docker-compose-plugin

# Tambahkan user ke grup docker (agar tidak perlu sudo)
sudo usermod -aG docker $USER
newgrp docker
```

---

## Persiapan Cloudflare

### 1. Arahkan Domain ke VPS

Di **Cloudflare Dashboard → DNS**:

| Type | Name     | Content        | Proxy Status     |
|------|----------|----------------|------------------|
| A    | `@`      | `<IP_VPS>`     | DNS only (abu-abu) ⚠️ |
| A    | `www`    | `<IP_VPS>`     | DNS only (abu-abu) ⚠️ |

> **Penting:** Set ke **"DNS only"** (bukan proxied/oranye) saat pertama kali issue SSL. Setelah SSL berhasil, boleh diubah ke **Proxied**.

### 2. Buat API Token Cloudflare

1. Buka [Cloudflare API Tokens](https://dash.cloudflare.com/profile/api-tokens)
2. Klik **Create Token → Custom Token**
3. Konfigurasi:
   - **Token name**: `Certbot DNS`
   - **Permissions**: `Zone → DNS → Edit`
   - **Zone Resources**: `Include → Specific zone → <domain-kamu>`
4. Klik **Continue to summary → Create Token**
5. Salin token — hanya tampil sekali!

### 3. SSL Mode di Cloudflare

Setelah SSL berhasil di-issue:
- **SSL/TLS → Overview → Full (Strict)**

---

## Persiapan VPS

### 1. Buka Firewall

```bash
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
sudo ufw status
```

### 2. Clone Repository

```bash
cd /opt
sudo git clone https://github.com/<username>/staflo_api.git staflo
sudo chown -R $USER:$USER /opt/staflo
cd /opt/staflo
```

---

## Clone & Konfigurasi

### 1. Salin dan Isi File .env

```bash
cp .env.production .env
nano .env
```

Isi semua nilai yang ditandai `<...>`:

```env
APP_URL=https://api.yourdomain.com
APP_DOMAIN=api.yourdomain.com
CERTBOT_EMAIL=admin@yourdomain.com

DB_PASSWORD=buatPasswordKuatDisini
DB_ROOT_PASSWORD=buatRootPasswordKuatDisini

MAIL_FROM_ADDRESS=noreply@yourdomain.com
RESEND_API_KEY=re_xxxxxxxxxxxx

FIREBASE_PROJECT_ID=your-firebase-project-id
```

Generate `APP_KEY`:

```bash
docker run --rm php:8.3-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
# Salin output ke APP_KEY di .env
```

### 2. Buat Cloudflare Credentials

```bash
cp docker/certbot/cloudflare.ini.example docker/certbot/cloudflare.ini
nano docker/certbot/cloudflare.ini
# Isi dengan API token Cloudflare kamu
chmod 600 docker/certbot/cloudflare.ini
```

Isi file:
```ini
dns_cloudflare_api_token = YOUR_CLOUDFLARE_API_TOKEN_HERE
```

### 3. Upload Firebase Service Account

```bash
mkdir -p storage/app/firebase
# Upload file service-account.json ke path ini:
# storage/app/firebase/service-account.json
```

Cara upload dari komputer lokal:
```bash
scp service-account.json user@<IP_VPS>:/opt/staflo/storage/app/firebase/
```

### 4. Buat Direktori yang Diperlukan

```bash
mkdir -p docker/certbot/certs docker/certbot/www
mkdir -p storage/app/public storage/framework/{cache/data,sessions,views} storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

---

## Issue SSL Certificate (Pertama Kali)

### Langkah 1: Jalankan Stack dengan Config HTTP-Only

Saat pertama kali, Nginx hanya perlu config HTTP karena SSL belum ada:

```bash
# Pastikan hanya app-http-only.conf yang aktif (app.conf belum dipakai)
ls docker/nginx/conf.d/
# Harus ada: app.conf dan app-http-only.conf

# Hapus sementara app.conf agar tidak konflik saat pertama kali
mv docker/nginx/conf.d/app.conf docker/nginx/conf.d/app.conf.ssl

# Jalankan stack
docker compose up -d db app nginx
```

### Langkah 2: Jalankan init-ssl.sh

```bash
chmod +x scripts/init-ssl.sh
./scripts/init-ssl.sh
```

Script ini akan:
1. Meminta sertifikat Let's Encrypt via Cloudflare DNS challenge
2. Menyimpan sertifikat ke `docker/certbot/certs/`
3. Mengaktifkan Nginx config HTTPS
4. Reload Nginx

### Langkah 3: Aktifkan Config SSL Lengkap

Setelah sertifikat berhasil:

```bash
# Kembalikan app.conf yang berisi HTTPS config
mv docker/nginx/conf.d/app.conf.ssl docker/nginx/conf.d/app.conf

# Render domain ke dalam config
DOMAIN=$(grep APP_DOMAIN .env | cut -d= -f2) envsubst '${DOMAIN}' \
  < docker/nginx/conf.d/app.conf > /tmp/app.conf.rendered
cp /tmp/app.conf.rendered docker/nginx/conf.d/app.conf

# Hapus config HTTP-only
rm docker/nginx/conf.d/app-http-only.conf

# Reload Nginx
docker compose exec nginx nginx -t
docker compose exec nginx nginx -s reload
```

---

## Deploy Aplikasi

### Deploy Pertama Kali (dengan seeder)

```bash
chmod +x scripts/deploy.sh
./scripts/deploy.sh --fresh
```

### Verifikasi

```bash
# Cek semua container berjalan
docker compose ps

# Cek log aplikasi
docker compose logs app --tail=50

# Test endpoint
curl -k https://api.yourdomain.com/up
# Harus return: OK atau {"status":"up"}

# Test API
curl https://api.yourdomain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"wrong"}'
```

---

## Update Aplikasi

Setiap kali ada perubahan kode:

```bash
cd /opt/staflo
./scripts/deploy.sh
```

Script `deploy.sh` akan:
1. `git pull origin main`
2. Build ulang Docker image
3. Restart container
4. Run `artisan config:cache`, `route:cache`, `view:cache`
5. Run `migrate --force`
6. Reload Nginx

---

## Renewal SSL Otomatis

### Setup Cron Job

```bash
crontab -e
```

Tambahkan baris berikut:
```cron
# Cek renewal setiap hari jam 03:00 dinihari
0 3 * * * /opt/staflo/scripts/renew-ssl.sh >> /var/log/certbot-renew.log 2>&1
```

Buat file log:
```bash
sudo touch /var/log/certbot-renew.log
sudo chown $USER:$USER /var/log/certbot-renew.log
chmod +x /opt/staflo/scripts/renew-ssl.sh
```

Test renewal (dry run):
```bash
docker compose run --rm certbot renew --dry-run \
  --dns-cloudflare \
  --dns-cloudflare-credentials /etc/letsencrypt/cloudflare.ini
```

---

## Monitoring & Troubleshooting

### Cek Status Container

```bash
docker compose ps
docker compose stats
```

### Lihat Log

```bash
# Semua service
docker compose logs -f

# Per service
docker compose logs app -f --tail=100
docker compose logs nginx -f
docker compose logs db -f
docker compose logs queue -f
```

### Masuk ke Container

```bash
# PHP app shell
docker compose exec app bash

# Artisan commands
docker compose exec app php artisan tinker
docker compose exec app php artisan queue:monitor

# MySQL
docker compose exec db mysql -u staflo_user -p staflo
```

### Restart Service

```bash
docker compose restart app
docker compose restart nginx
docker compose restart queue
```

### Masalah Umum

| Masalah | Solusi |
|--------|--------|
| `502 Bad Gateway` | `docker compose restart app` lalu cek `docker compose logs app` |
| SSL cert tidak ditemukan | Pastikan `docker/certbot/certs/live/<domain>/` ada |
| DB connection refused | Tunggu health check MySQL: `docker compose ps db` |
| Storage permission error | `docker compose exec app chmod -R 775 storage` |
| Queue worker crash | `docker compose restart queue`, cek `docker compose logs queue` |
| Artisan cache error | `docker compose exec app php artisan config:clear` |

---

## Struktur File Docker

```
staflo_api/
├── Dockerfile                        # Multi-stage build (Node + PHP-FPM)
├── docker-compose.yml                # Orchestrasi semua service
├── .dockerignore                     # File yang diabaikan saat build
├── .env.production                   # Template env untuk produksi
│
├── docker/
│   ├── nginx/
│   │   ├── nginx.conf                # Konfigurasi global Nginx
│   │   └── conf.d/
│   │       ├── app.conf              # Virtual host HTTPS (aktif setelah SSL)
│   │       └── app-http-only.conf    # Virtual host HTTP (dipakai saat init SSL)
│   ├── php/
│   │   ├── php.ini                   # Konfigurasi PHP production
│   │   └── www.conf                  # PHP-FPM pool configuration
│   ├── mysql/
│   │   └── my.cnf                    # Konfigurasi MySQL
│   └── certbot/
│       ├── cloudflare.ini.example    # Template API token Cloudflare
│       ├── cloudflare.ini            # File API token (JANGAN commit!)
│       ├── certs/                    # Sertifikat Let's Encrypt (volume)
│       └── www/                     # ACME challenge webroot (volume)
│
└── scripts/
    ├── init-ssl.sh                   # Issue SSL pertama kali
    ├── renew-ssl.sh                  # Renewal sertifikat (via cron)
    └── deploy.sh                     # Deploy / update aplikasi
```

---

## Catatan Keamanan

- File `docker/certbot/cloudflare.ini` dan `.env` **JANGAN** di-commit ke Git
- Tambahkan ke `.gitignore`:
  ```
  .env
  docker/certbot/cloudflare.ini
  docker/certbot/certs/
  storage/app/firebase/
  ```
- Gunakan password yang kuat untuk MySQL (`DB_PASSWORD`, `DB_ROOT_PASSWORD`)
- Aktifkan **Full (Strict)** SSL mode di Cloudflare
- Pertimbangkan mengaktifkan **Cloudflare Bot Fight Mode** untuk perlindungan tambahan

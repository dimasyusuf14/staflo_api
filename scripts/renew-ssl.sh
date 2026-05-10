#!/usr/bin/env bash
# =============================================================================
# renew-ssl.sh — Renew SSL certificates (run via cron)
#
# Add to crontab on the VPS:
#   0 3 * * * /path/to/project/scripts/renew-ssl.sh >> /var/log/certbot-renew.log 2>&1
# =============================================================================
set -euo pipefail

cd "$(dirname "$0")/.."

echo "[$(date)] Starting certificate renewal..."

docker compose run --rm certbot renew \
  --dns-cloudflare \
  --dns-cloudflare-credentials /etc/letsencrypt/cloudflare.ini \
  --dns-cloudflare-propagation-seconds 30

echo "[$(date)] Reloading Nginx..."
docker compose exec nginx nginx -s reload

echo "[$(date)] Certificate renewal complete."

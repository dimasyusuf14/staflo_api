#!/usr/bin/env bash
# =============================================================================
# deploy.sh — Deploy / update the application on the VPS
#
# Usage:
#   First deploy : ./scripts/deploy.sh --fresh
#   Update       : ./scripts/deploy.sh
# =============================================================================
set -euo pipefail

FRESH=false
for arg in "$@"; do
  [[ "$arg" == "--fresh" ]] && FRESH=true
done

cd "$(dirname "$0")/.."

echo "==> [1/7] Pulling latest code..."
git pull origin main

echo "==> [2/7] Building Docker images..."
docker compose build --no-cache

echo "==> [3/7] Starting/recreating containers..."
docker compose up -d --remove-orphans

echo "==> [4/7] Waiting for database to be ready..."
sleep 5

echo "==> [5/7] Running artisan commands..."
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan migrate --force

if $FRESH; then
  echo "==> [5b] Running seeders (fresh deploy)..."
  docker compose exec app php artisan db:seed --force
fi

echo "==> [6/7] Creating storage symlink..."
docker compose exec app php artisan storage:link || true

echo "==> [7/7] Reloading Nginx..."
docker compose exec nginx nginx -s reload || true

echo ""
echo "==> Deployment complete!"
docker compose ps

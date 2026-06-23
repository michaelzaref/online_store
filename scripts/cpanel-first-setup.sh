#!/usr/bin/env bash
# One-time setup — run ON the Namecheap server via SSH (cPanel Terminal).
set -euo pipefail

APP_DIR="/home/elitjaio/online_store"
REPO="https://github.com/michaelzaref/online_store.git"
PHP_BIN="${PHP_BIN:-php}"

echo "==> Cloning repository..."
if [ ! -d "$APP_DIR/.git" ]; then
  git clone "$REPO" "$APP_DIR"
fi

cd "$APP_DIR"

if [ ! -f .env ]; then
  cp .env.example .env
  echo ""
  echo "IMPORTANT: Edit .env before continuing:"
  echo "  nano $APP_DIR/.env"
  echo ""
  echo "Set at minimum:"
  echo "  APP_ENV=production"
  echo "  APP_DEBUG=false"
  echo "  APP_URL=https://elite-store.online"
  echo "  DB_* values from cPanel MySQL"
  echo ""
  exit 1
fi

$PHP_BIN artisan key:generate --force 2>/dev/null || true

if command -v composer >/dev/null 2>&1; then
  composer install --no-dev --optimize-autoloader --no-interaction
fi

$PHP_BIN artisan migrate --force
$PHP_BIN artisan storage:link
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

chmod -R 775 storage bootstrap/cache
chmod +x scripts/deploy-cpanel.sh

echo ""
echo "==> Next steps in cPanel:"
echo "  1. Domains → elite-store.online → Document Root:"
echo "     $APP_DIR/public"
echo "  2. MultiPHP Manager → PHP 8.3 for this domain"
echo "  3. SSL/TLS Status → enable AutoSSL"
echo "  4. Git Version Control → webhook to auto-pull on push"
echo ""

#!/usr/bin/env bash
# Run on Namecheap cPanel after git pull (webhook or manual).
set -euo pipefail

APP_DIR="${APP_DIR:-/home/elitjaio/online_store}"
cd "$APP_DIR"

# Prefer PHP 8.3+ (Laravel 13). Fall back to default php.
PHP_BIN=""
for candidate in \
  /opt/cpanel/ea-php83/root/usr/bin/php \
  /opt/cpanel/ea-php84/root/usr/bin/php \
  /usr/local/bin/ea-php83 \
  /usr/local/bin/ea-php84 \
  php; do
  if command -v "$candidate" >/dev/null 2>&1 && "$candidate" -r 'exit(version_compare(PHP_VERSION, "8.3.0", ">=") ? 0 : 1);' 2>/dev/null; then
    PHP_BIN="$candidate"
    break
  fi
done

if [ -z "$PHP_BIN" ]; then
  PHP_BIN="php"
  echo "WARNING: PHP 8.3+ not found. Enable PHP 8.3 in cPanel → MultiPHP Manager."
  "$PHP_BIN" -v | head -1
fi

echo "==> Using: $($PHP_BIN -v | head -1)"

echo "==> Pulling latest code..."
git pull origin main

COMPOSER="composer"
if [ ! -x "$(command -v composer 2>/dev/null)" ] && [ -f "$APP_DIR/composer.phar" ]; then
  COMPOSER="$PHP_BIN $APP_DIR/composer.phar"
fi

echo "==> Installing dependencies..."
$COMPOSER install --no-dev --optimize-autoloader --no-interaction

echo "==> Migrating database..."
$PHP_BIN artisan migrate --force

$PHP_BIN artisan storage:link 2>/dev/null || true
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan filament:optimize 2>/dev/null || true

chmod -R 775 storage bootstrap/cache

echo "==> Deploy complete: https://elite-store.online"

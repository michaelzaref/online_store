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

# Sync cPanel public_html (document root) with Laravel app
WEB_DIR="/home/elitjaio/public_html"
mkdir -p "$WEB_DIR"
cp "$APP_DIR/public/.htaccess" "$WEB_DIR/.htaccess"
cat > "$WEB_DIR/index.php" << 'PHP'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../online_store/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../online_store/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../online_store/bootstrap/app.php';

$app->handleRequest(Request::capture());
PHP
for item in build css js storage favicon.ico robots.txt; do
  if [ -e "$APP_DIR/public/$item" ]; then
    ln -sfn "$APP_DIR/public/$item" "$WEB_DIR/$item"
  fi
done
chmod 755 "$WEB_DIR"
chmod 644 "$WEB_DIR/index.php" "$WEB_DIR/.htaccess"

echo "==> Deploy complete: https://elite-store.online"

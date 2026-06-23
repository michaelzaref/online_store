#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
export LD_LIBRARY_PATH="$ROOT/.local/lib:${LD_LIBRARY_PATH:-}"

bash "$ROOT/scripts/mysql-start.sh"

cd "$ROOT"
php artisan config:clear

PORT="${1:-8002}"
while ss -tln | grep -q ":${PORT} "; do
  PORT=$((PORT + 1))
done

echo ""
echo "BegoStore is running:"
echo "  Store: http://127.0.0.1:${PORT}"
echo "  Admin: http://127.0.0.1:${PORT}/admin"
echo "  Login: admin@begostore.com / password"
echo ""
php artisan serve --host=127.0.0.1 --port="$PORT"

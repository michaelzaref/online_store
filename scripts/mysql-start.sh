#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
export LD_LIBRARY_PATH="$ROOT/.local/lib:${LD_LIBRARY_PATH:-}"
CNF="$ROOT/.mysql/my.cnf"
PID_FILE="$ROOT/.mysql/run/mysqld.pid"
MYSQL_HOME="$ROOT/.local/mysql-8.0.45-linux-glibc2.28-x86_64"
MYSQLD="$MYSQL_HOME/bin/mysqld"
MYSQL="$MYSQL_HOME/bin/mysql"

mkdir -p "$ROOT/.mysql/data" "$ROOT/.mysql/run" "$ROOT/.mysql/logs"
chmod 600 "$CNF"

if [[ ! -x "$MYSQLD" ]]; then
  echo "Bundled MySQL not found. Run: bash scripts/install-mysql.sh"
  exit 1
fi

if [[ ! -d "$ROOT/.mysql/data/mysql" ]]; then
  echo "Initializing local MySQL..."
  "$MYSQLD" --defaults-file="$CNF" --initialize-insecure
fi

if [[ -f "$PID_FILE" ]] && kill -0 "$(cat "$PID_FILE")" 2>/dev/null; then
  echo "Local MySQL is already running (PID $(cat "$PID_FILE"))."
  exit 0
fi

echo "Starting local MySQL on port 3309..."
"$MYSQLD" --defaults-file="$CNF" &

for i in {1..30}; do
  if "$MYSQL" --defaults-file="$CNF" -uroot -e "SELECT 1" >/dev/null 2>&1; then
    echo "MySQL is ready on port 3309."
    exit 0
  fi
  sleep 1
done

echo "MySQL failed to start. Check $ROOT/.mysql/logs/error.log"
exit 1

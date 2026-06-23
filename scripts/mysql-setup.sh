#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
CNF="$ROOT/.mysql/my.cnf"
PID_FILE="$ROOT/.mysql/run/mysqld.pid"
MYSQL="$ROOT/.local/mysql-8.0.45-linux-glibc2.28-x86_64/bin/mysql"

"$ROOT/scripts/mysql-start.sh"

"$MYSQL" --defaults-file="$CNF" -uroot <<'SQL'
CREATE DATABASE IF NOT EXISTS watch_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'watchstore'@'localhost' IDENTIFIED BY 'watchstore';
CREATE USER IF NOT EXISTS 'watchstore'@'127.0.0.1' IDENTIFIED BY 'watchstore';
GRANT ALL PRIVILEGES ON watch_store.* TO 'watchstore'@'localhost';
GRANT ALL PRIVILEGES ON watch_store.* TO 'watchstore'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL

echo "Database watch_store is ready."

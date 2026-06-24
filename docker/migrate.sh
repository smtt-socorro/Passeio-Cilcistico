#!/bin/bash
set -euo pipefail

PGHOST="${DB_HOST:-db}"
PGPORT="${DB_PORT:-5432}"
PGUSER="${DB_USER:-smtt}"
PGPASSWORD="${DB_PASS:-}"
PGDATABASE="${DB_NAME:-smtt}"

export PGPASSWORD

run_sql() {
    local file="$1"
    echo ">> Aplicando $(basename "$file")..."
    psql -h "$PGHOST" -p "$PGPORT" -U "$PGUSER" -d "$PGDATABASE" -v ON_ERROR_STOP=1 -f "$file"
}

for file in /migrations/02_functions.sql /migrations/03_triggers.sql /migrations/04_admin_seed.sql; do
    if [ -f "$file" ]; then
        run_sql "$file"
    fi
done

echo "Migrations concluídas."

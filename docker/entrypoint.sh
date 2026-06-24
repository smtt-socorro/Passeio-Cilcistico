#!/bin/bash
set -euo pipefail

if [ "${SKIP_DEPLOY_CHECK:-false}" != "true" ]; then
    if [ -z "${DEPLOY_CHECKSUM:-}" ] || [ -z "${DEPLOY_SALT:-}" ] || [ -z "${APP_DOMAIN:-}" ]; then
        echo "Falha na inicialização do ambiente: parâmetros de deploy ausentes." >&2
        exit 1
    fi

    EXPECTED=$(printf '%s' "${APP_DOMAIN}:${DEPLOY_SALT}" | sha256sum | awk '{print $1}')

    if [ "$DEPLOY_CHECKSUM" != "$EXPECTED" ]; then
        echo "Falha na inicialização do ambiente: checksum inválido." >&2
        exit 1
    fi
fi

if [ -n "${DB_HOST:-}" ]; then
    DB_PORT="${DB_PORT:-5432}"
    echo "Aguardando PostgreSQL em ${DB_HOST}:${DB_PORT}..."

    for _ in $(seq 1 60); do
        if php -r "
            try {
                \$pdo = new PDO(
                    'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_NAME'),
                    getenv('DB_USER'),
                    getenv('DB_PASS'),
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                exit(0);
            } catch (Throwable \$e) {
                exit(1);
            }
        "; then
            break
        fi
        sleep 2
    done
fi

exec "$@"

#!/bin/bash
set -euo pipefail

APP_DOMAIN="${1:-}"
DEPLOY_SALT="${2:-}"

if [ -z "$APP_DOMAIN" ] || [ -z "$DEPLOY_SALT" ]; then
    echo "Uso: $0 <APP_DOMAIN> <DEPLOY_SALT>"
    echo "Exemplo: $0 evento.exemplo.gov.br \$(uuidgen)"
    exit 1
fi

CHECKSUM=$(printf '%s' "${APP_DOMAIN}:${DEPLOY_SALT}" | sha256sum | awk '{print $1}')

cat <<EOF
APP_DOMAIN=${APP_DOMAIN}
DEPLOY_SALT=${DEPLOY_SALT}
DEPLOY_CHECKSUM=${CHECKSUM}
EOF

#!/bin/bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="${1:-${ROOT_DIR}/.env.deploy}"

if [ ! -f "$ENV_FILE" ]; then
    echo "Arquivo não encontrado: $ENV_FILE"
    echo "Copie .env.deploy.example para .env.deploy e preencha os valores."
    exit 1
fi

set -a
# shellcheck disable=SC1090
source "$ENV_FILE"
set +a

if [ -z "${DEPLOY_ID:-}" ]; then
    echo "DEPLOY_ID é obrigatório em $ENV_FILE"
    exit 1
fi

VOLUME_NAME="smtt_pgdata_${DEPLOY_ID}"

if ! docker volume inspect "$VOLUME_NAME" >/dev/null 2>&1; then
    echo ">> Criando volume $VOLUME_NAME"
    docker volume create "$VOLUME_NAME"
fi

cd "$ROOT_DIR"

docker compose \
    -f docker-compose.yml \
    -f compose.deploy.yml \
    --env-file "$ENV_FILE" \
    --profile runtime \
    up -d

echo "Deploy concluído."
echo "App: http://localhost:${APP_PORT:-8080}"

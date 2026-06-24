#!/bin/bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
IMAGE="${SMTT_APP_IMAGE:-wendellsales/bikesmtt}"
TAG="${1:-latest}"

if command -v docker >/dev/null 2>&1 && docker info >/dev/null 2>&1; then
    CONTAINER_CMD=(docker)
elif command -v podman >/dev/null 2>&1; then
    CONTAINER_CMD=(podman)
else
    echo "Docker ou Podman não encontrado."
    exit 1
fi

cd "$ROOT_DIR"

echo ">> Build docker.io/${IMAGE}:${TAG}"
"${CONTAINER_CMD[@]}" build -t "docker.io/${IMAGE}:${TAG}" .

if [ "$TAG" = "latest" ]; then
    "${CONTAINER_CMD[@]}" tag "docker.io/${IMAGE}:${TAG}" "docker.io/${IMAGE}:1.0.0"
fi

echo ">> Push docker.io/${IMAGE}:${TAG}"
"${CONTAINER_CMD[@]}" push "docker.io/${IMAGE}:${TAG}"

if [ "$TAG" = "latest" ]; then
    "${CONTAINER_CMD[@]}" push "docker.io/${IMAGE}:1.0.0"
fi

echo "Imagem publicada: docker.io/${IMAGE}:${TAG}"

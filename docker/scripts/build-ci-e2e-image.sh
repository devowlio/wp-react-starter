#!/bin/sh

if [ -z "$VERSION" ]; then
    VERSION=$(node -e "console.log(require('./package.json').version)")
fi

CONTAINER_NAME=$(node -e "console.log(require('./package.json')['docker-ci-e2e-image-name'] || '')")

echo "docker build --cache-from \"$CONTAINER_NAME:latest\" -t \"$CONTAINER_NAME:$VERSION\" \"$@\" ./docker/container/ci-e2e"
docker build --cache-from "$CONTAINER_NAME:latest" -t "$CONTAINER_NAME:$VERSION" "$@" ./docker/container/ci-e2e
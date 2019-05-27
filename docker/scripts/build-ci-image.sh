#!/bin/sh

if [ -z "$VERSION" ]; then
    VERSION=$(node -e "console.log(require('./package.json').version)")
fi

CONTAINER_NAME=$(node -e "console.log(require('../package.json')['docker-ci-image-name'] || '')")

docker build -t "$CONTAINER_NAME:$VERSION" "$@" ./docker/container/ci

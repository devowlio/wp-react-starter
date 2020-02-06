#!/bin/sh

# Purge a given composition of docker containers with volumes and networks. Useful for review applications and E2E tests.

# Remove running containers
echo "[CONTAINERS]"
# docker ps -a
export CURRENT_CONTAINERS="$(docker ps -a --format "{{.ID}} {{.Names}}" | awk '$2~/'"$COMPOSE_PROJECT_NAME$COMPOSE_PROJECT_NAME_SUFFIX-$CI_COMMIT_REF_SLUG"'/{print $1}')"
test "$CURRENT_CONTAINERS" && echo "Removing..." && docker rm -f -v $CURRENT_CONTAINERS

# Remove available volumes
echo
echo "[VOLUMES]"
# docker volume ls
export CURRENT_VOLUMES="$(docker volume ls --format "{{.Name}}" | awk '$1~/'"$COMPOSE_PROJECT_NAME$COMPOSE_PROJECT_NAME_SUFFIX-$CI_COMMIT_REF_SLUG"'/{print $1}')"
test "$CURRENT_VOLUMES" && echo "Removing..." && docker volume remove -f $CURRENT_VOLUMES

# Remove available networks
echo
echo "[NETWORKS]"
# docker network ls
export CURRENT_NETWORKS="$(docker network list --format "{{.Name}}" | awk '$1~/'"$COMPOSE_PROJECT_NAME$COMPOSE_PROJECT_NAME_SUFFIX-$CI_COMMIT_REF_SLUG"'/{print $1}')"
test "$CURRENT_NETWORKS" && echo "Removing..." && docker network remove $CURRENT_NETWORKS

echo
echo "Purged"
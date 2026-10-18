#!/bin/sh
# Runs the installer in the container of `compose.yaml`, as `composer create-project` would on a machine with PHP:
#
#     docker compose run --rm app sh installer/docker.sh
#
# The database is the container's, so only the project's name, the preset and the first user are asked. The folder
# keeps its git history, if it has one, rather than getting an "Initial commit".
set -e

composer install --no-interaction --no-progress

INSTALL_DOCKER=1 INSTALL_DB_HOST=db INSTALL_DB_NAME=website INSTALL_DB_USER="$DB_USER" \
    INSTALL_DB_PASSWORD="$DB_PASSWORD" INSTALL_GIT=0 composer run-script post-create-project-cmd

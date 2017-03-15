#!/bin/sh

export DOCKER_HOST_IP=$(docker-machine inspect --format '{{ .Driver.HostOnlyCIDR}}' | awk -F/ '{print $1}')

docker-compose exec mysql /scripts/setup-mysql-server.sh

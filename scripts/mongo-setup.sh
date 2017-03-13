#!/bin/sh

export DOCKER_HOST_IP=$(docker-machine inspect --format '{{ .Driver.HostOnlyCIDR}}' | awk -F/ '{print $1}')

docker-compose exec mongo /scripts/setup-mongo-server.sh

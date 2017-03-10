#!/bin/sh

export APPLICATION_ENV=dev
export DOCKER_HOST_IP=$(docker-machine inspect --format '{{ .Driver.HostOnlyCIDR}}' | awk -F/ '{print $1}')

if [ -e .env ]
then
    source .env
fi

docker-compose up

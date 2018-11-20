#!/bin/sh

export APPLICATION_ENV=dev

export HOST_IP=$(ipconfig getifaddr en0)

echo $DOCKER_IP

if [ -e .env ]
then
    source .env
fi

docker-compose up

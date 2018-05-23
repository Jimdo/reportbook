#!/bin/sh

export APPLICATION_ENV=dev

if [ -e .env ]
then
    source .env
fi

docker-compose up

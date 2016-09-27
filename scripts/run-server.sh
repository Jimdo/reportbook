#!/bin/sh

if [ -z ${MONGO_HOST+x} ]; then
  export MONGO_HOST=$(docker-machine ip)
fi

if [ -z ${APPLICATION_ENV+x} ]; then
    export APPLICATION_ENV=dev
fi

export APPLICATION_ENV=dev

php -S localhost:8000 -t app/ app/router.php

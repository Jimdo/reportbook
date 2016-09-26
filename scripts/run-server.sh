#!/bin/sh

if [ -z ${MONGO_SERVER_IP+x} ]; then
  export MONGO_SERVER_IP=$(docker-machine ip)
fi

if [ -z ${MONGO_ADMIN_PASSWORD+x} ]; then
  export MONGO_ADMIN_PASSWORD=geheim
fi

if [ -z ${MONGO_USERNAME+x} ]; then
  export MONGO_USERNAME=reportbook-dev
fi

if [ -z ${MONGO_PASSWORD+x} ]; then
  export MONGO_PASSWORD=geheim
fi

if [ -z ${MONGO_DATABASE+x} ]; then
  export MONGO_DATABASE=reportbook-dev
fi

if [ -z ${APPLICATION_ENV+x} ]; then
    export APPLICATION_ENV=dev
fi

php -S localhost:8000 -t app/ app/router.php

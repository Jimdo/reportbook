#!/bin/sh

if [ -z ${MONGO_SERVER_IP+x} ]; then
  export MONGO_IP=$(docker-machine ip)
fi

export APPLICATION_ENV=testing

scripts/phpunit

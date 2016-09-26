#!/bin/sh

if [ -z ${MONGO_HOST+x} ]; then
  export MONGO_HOST=$(docker-machine ip)
fi

export APPLICATION_ENV=test

scripts/phpunit

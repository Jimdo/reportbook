#!/bin/sh

if [ -z ${MONGO_SERVER_IP+x} ]; then
  export MONGO_SERVER_IP=$(docker-machine ip)
fi

scripts/phpunit

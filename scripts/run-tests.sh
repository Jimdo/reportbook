#!/bin/sh

if [ -z ${MONGO_SERVER_IP+x} ]; then
  export MONGO_SERVER_IP=$(docker-machine ip)
fi

export MONGO_DATABASE=reportbook-test
export MONGO_USERNAME=reportbook-test
export MONGO_PASSWORD=geheim

scripts/phpunit

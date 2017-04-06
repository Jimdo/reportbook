#!/bin/sh

if [ -z ${MONGO_HOST+x} ]; then
  export MONGO_HOST=$(docker-machine ip)
fi

if [ -z ${MYSQL_HOST+x} ]; then
  export MYSQL_HOST=127.0.0.1
fi

if [ -z ${REPORTBOOK_IP+x} ]; then
  export REPORTBOOK_IP=$(docker-machine ip)
fi

export APPLICATION_ENV=test

./scripts/phpunit

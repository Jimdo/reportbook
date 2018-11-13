#!/bin/sh

if [ -z ${MONGO_HOST+x} ]; then
  export MONGO_HOST=$(docker-machine ip)
fi

if [ -z ${MYSQL_HOST+x} ]; then
  export MYSQL_HOST=$(docker-machine ip)
fi

if [ -z ${SELENIUM_IP+x} ]; then
  export SELENIUM_IP=$(docker-machine ip)
fi

export APPLICATION_ENV=test

docker-compose run reportbook scripts/phpunit --testsuite $1

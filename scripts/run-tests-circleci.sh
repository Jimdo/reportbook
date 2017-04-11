#!/bin/sh

if [ -z ${MONGO_HOST+x} ]; then
  export MONGO_HOST=$(docker-machine ip)
fi

if [ -z ${MYSQL_HOST+x} ]; then
  export MYSQL_HOST=$(docker-machine ip)
fi

if [ -z ${REPORTBOOK_IP+x} ]; then
  export REPORTBOOK_IP=$(docker-machine ip)
fi

export APPLICATION_ENV=test

export REPORTBOOK_IP=$(ip addr show docker0 | grep "inet\b" | awk '{print $2}' | cut -d/ -f1)

./scripts/phpunit

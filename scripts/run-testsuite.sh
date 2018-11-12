#!/bin/sh

if [ -z ${MONGO_HOST+x} ]; then
  export MONGO_HOST=$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.Gateway}}{{end}}' reportbook_reportbook_1)
fi

if [ -z ${MYSQL_HOST+x} ]; then
  export MYSQL_HOST=$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.Gateway}}{{end}}' reportbook_reportbook_1)
fi

if [ -z ${SELENIUM_IP+x} ]; then
  export SELENIUM_IP=$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.Gateway}}{{end}}' reportbook_reportbook_1)
fi

export APPLICATION_ENV=test

export REPORTBOOK_IP=$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.Gateway}}{{end}}' reportbook_reportbook_1)

export DOCKER_HOST_IP=$(docker-machine inspect --format '{{ .Driver.HostOnlyCIDR}}' | awk -F/ '{print $1}')

docker-compose run reportbook scripts/phpunit --testsuite $1

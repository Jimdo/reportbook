#!/bin/sh

if [ -z ${MONGO_HOST+x} ]; then
  export MONGO_HOST=$(docker-machine ip)
fi

if [ -z ${MYSQL_HOST+x} ]; then
  export MYSQL_HOST=$(docker-machine ip)
fi

export APPLICATION_ENV=test

docker run -it --rm \
    -e APPLICATION_ENV=$APPLICATION_ENV \
    -e MONGO_HOST=$MONGO_HOST \
    -e MYSQL_HOST=$MYSQL_HOST \
    -v $(PWD)/:/var/www/ \
    --add-host="docker_host:$(docker-machine inspect --format '{{ .Driver.HostOnlyCIDR}}' | awk -F/ '{print $1}')" \
    jimdo/reportbook:debug \
    scripts/phpunit

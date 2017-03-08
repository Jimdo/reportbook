#!/bin/sh

if [ -e .env ]
then
    source .env
fi

if [ -z ${APPLICATION_ENV+x} ]; then
  APPLICATION_ENV=dev
fi

if [ -z ${MYSQL_ROOT_PASSWORD+x} ]; then
  MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD
fi

eval $(docker-machine env)

mysql -u root -p$MYSQL_ROOT_PASSWORD -h $(docker-machine ip)

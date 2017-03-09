#!/bin/sh

if [ -e .env ]
then
    source .env
fi

docker run -it --rm \
    -p 3306:3306 \
    --name mysql \
    -e MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD \
    -e MYSQL_DATABASE=$MYSQL_DATABASE \
    -e MYSQL_USER=$MYSQL_USER \
    -e MYSQL_PASSWORD=$MYSQL_PASSWORD \
    -v reportbook-data-mysql:/var/lib/mysql \
    mysql:latest
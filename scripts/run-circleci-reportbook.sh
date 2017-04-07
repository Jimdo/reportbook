#!/bin/sh

APPLICATION_ENV=dev

if [ -e .env ]
then
    source .env
fi

export DOCKER_HOST_IP=$(route -n | awk '/UG[ \t]/{print $2}')

docker run --net="host" -d \
    -p 80:80 \
    -e APPLICATION_ENV=$APPLICATION_ENV \
    -e MONGO_HOST=$DOCKER_HOST_IP \
    -e MYSQL_HOST=$DOCKER_HOST_IP \
    -e MYSQL_DATABASE=$MYSQL_DATABASE \
    -e MYSQL_USER=$MYSQL_USER \
    -e MYSQL_PASSWORD=$MYSQL_PASSWORD \
    -e MAILGUN_DOMAIN=$MAILGUN_DOMAIN \
    -e MAILGUN_KEY=$MAILGUN_KEY \
    -v $(pwd)/:/var/www/ \
    jimdo/reportbook
#!/bin/sh

APPLICATION_ENV=dev

if [ -e .env ]
then
    source .env
fi

if [ -z ${DEBUG_IF+x} ]; then
    echo "In order to use debugging capabilities you"
    echo "must set DEBUG_IF to a valid network interface!"
    exit
fi

docker run -it --rm \
    -p 80:80 \
    -e APPLICATION_ENV='dev' \
    -e MONGO_HOST=$(docker-machine ip) \
    -e MAILGUN_DOMAIN=$MAILGUN_DOMAIN \
    -e MAILGUN_KEY=$MAILGUN_KEY \
    -v $(PWD)/:/var/www/ \
    --add-host="docker_host:$(ifconfig $DEBUG_IF | awk '$1 == "inet" {print $2}')" \
    jimdo/reportbook

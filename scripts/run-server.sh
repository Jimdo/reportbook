#!/bin/sh

APPLICATION_ENV=dev

if [ -e .env ]
then
    source .env
fi

docker run -it --rm \
    -p 80:80 \
    -e APPLICATION_ENV='dev' \
    -e MONGO_HOST=$(docker-machine ip) \
    -e MAILGUN_DOMAIN=$MAILGUN_DOMAIN \
    -e MAILGUN_KEY=$MAILGUN_KEY \
    -v $(PWD)/:/var/www/ \
    --add-host="docker_host:$(docker-machine inspect jimdo --format '{{ .Driver.HostOnlyCIDR}}' | awk -F/ '{print $1}')" \
    jimdo/reportbook:debug

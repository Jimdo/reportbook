#!/bin/sh

APPLICATION_ENV=dev

docker run -it --rm \
    -p 80:80 \
    -e APPLICATION_ENV='dev' \
    -e MONGO_HOST=$(docker-machine ip) \
    -e MAILGUN_DOMAIN=$MAILGUN_DOMAIN \
    -e MAILGUN_KEY=$MAILGUN_KEY \
    -v $(PWD)/:/var/www/ \
    jimdo/reportbook

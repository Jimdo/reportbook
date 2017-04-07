#!/bin/sh

APPLICATION_ENV=dev

if [ -e .env ]
then
    source .env
fi

docker run -d --net=host \
    -p 80:80 \
    -p 27017:27017 \
    -e APPLICATION_ENV=$APPLICATION_ENV \
    -e MONGO_HOST=$MONGO_HOST \
    -e MYSQL_HOST=$MYSQL_HOST \
    -e MYSQL_DATABASE=$MYSQL_DATABASE \
    -e MYSQL_USER=$MYSQL_USER \
    -e MYSQL_PASSWORD=$MYSQL_PASSWORD \
    -e MAILGUN_DOMAIN=$MAILGUN_DOMAIN \
    -e MAILGUN_KEY=$MAILGUN_KEY \
    -v $(pwd)/:/var/www/ \
    -v ./tests/:/var/www/tests \
    jimdo/reportbook

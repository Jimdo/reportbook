#!/bin/sh

APPLICATION_ENV=dev

if [ -e .env ]
then
    source .env
fi

docker run -d \
    -p 90:80 \
    -e APPLICATION_ENV=$APPLICATION_ENV \
    -e MONGO_HOST=$MONGO_HOST \
    -e MYSQL_HOST=$MYSQL_HOST \
    -e MYSQL_DATABASE=$MYSQL_DATABASE \
    -e MYSQL_USER=$MYSQL_USER \
    -e MYSQL_PASSWORD=$MYSQL_PASSWORD \
    -e MAILGUN_DOMAIN=$MAILGUN_DOMAIN \
    -e MAILGUN_KEY=$MAILGUN_KEY \
    -e REPORTBOOK_IP=$REPORTBOOK_IP \
    -v $(pwd)/:/var/www/ \
    jimdo/reportbook
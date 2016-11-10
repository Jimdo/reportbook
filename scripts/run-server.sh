#!/bin/sh

APPLICATION_ENV=dev

docker run -it --rm \
    -p 80:80 \
    -e APPLICATION_ENV='dev' \
    -e MONGO_HOST=$(docker-machine ip) \
    -v $(PWD)/:/var/www/ \
    jimdo/reportbook

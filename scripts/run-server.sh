#!/bin/sh

if [ -z ${MONGO_SERVER_IP+x} ]; then
  export MONGO_SERVER_IP=$(docker-machine ip)
fi

php -S localhost:8000 -t app/ app/router.php

#!/bin/sh

if [ -e ./scripts/set-env-variables.sh ]
then
    source ./scripts/set-env-variables.sh
fi

export APPLICATION_ENV=test

docker-compose run reportbook scripts/phpunit $1

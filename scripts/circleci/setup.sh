#!/bin/bash

export APPLICATION_ENV=test

if [ -e .env ]
then
    source .env
fi

apt-get install -y mongodb-clients

# Setup mongo user and collections
./scripts/circleci/setup-mongo.sh

# Setup mysql user and database
./scripts/circleci/setup-mysql.sh

# ./scripts/phpunit

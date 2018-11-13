#!/bin/sh

set -e

## Setup mongo user and collections
./scripts/circleci/setup-mongo.sh

## Setup mysql user and database
./scripts/circleci/setup-mysql.sh

echo "extension=mongodb.so" >> /opt/circleci/php/$(php -r 'echo phpversion();')/etc/php.ini

#!/bin/sh

set -e

echo "extension=mongodb.so" >> /opt/circleci/php/$(php -r 'echo phpversion();')/etc/php.ini

## Setup mongo user and collections
./scripts/circleci-mongo-setup.sh

## Setup mysql user and database
./scripts/circleci-mysql-setup.sh

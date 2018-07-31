#!/bin/sh

set -e

# echo "extension=mongodb.so" >> /opt/circleci/php/$(php -r 'echo phpversion();')/etc/php.ini
echo "extension=mongodb.so" | sudo tee /usr/local/etc/php/php.ini > /dev/null

## Setup mongo user and collections
./scripts/circleci/circleci-mongo-setup.sh

## Setup mysql user and database
./scripts/circleci/circleci-mysql-setup.sh

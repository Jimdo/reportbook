#!/bin/sh

set -e

## Setup mongo user and collections
./scripts/circleci-mongo-setup.sh

## Setup mysql user and database
./scripts/circleci-mysql-setup.sh

echo "extension=mongodb.so" >> /opt/circleci/php/7.0.4/etc/php.ini
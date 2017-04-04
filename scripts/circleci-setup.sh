#!/bin/sh

set -e

# Install Selenium.
curl http://selenium-release.storage.googleapis.com/2.53/selenium-server-standalone-2.53.1.jar > selenium-server-standalone.jar

curl http://chromedriver.storage.googleapis.com/2.23/chromedriver_linux64.zip | gzip -dc > chromedriver

chmod +x chromedriver

## Setup mongo user and collections
./scripts/circleci-mongo-setup.sh

## Setup mysql user and database
./scripts/circleci-mysql-setup.sh

echo "extension=mongodb.so" >> /opt/circleci/php/7.0.4/etc/php.ini
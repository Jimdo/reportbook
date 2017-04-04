#!/bin/sh

set -e

# Install Selenium.
curl http://selenium-release.storage.googleapis.com/2.53/selenium-server-standalone-2.53.1.jar > selenium-server-standalone.jar

curl http://chromedriver.storage.googleapis.com/2.23/chromedriver_linux64.zip | gzip -dc > chromedriver

chmod +x chromedriver

javaw -jar selenium-server-standalone.jar -trustAllSSLCertificates -Dwebdriver.chrome.driver=chromedriver

# Update Google Chrome.
wget -q -O https://dl-ssl.google.com/linux/linux_signing_key.pub | sudo apt-key add -

sudo sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb stable main" >> /etc/apt/sources.list.d/google.list'

sudo apt-get update

sudo apt-get --only-upgrade install google-chrome-stable

## Setup mongo user and collections
./scripts/circleci-mongo-setup.sh

## Setup mysql user and database
./scripts/circleci-mysql-setup.sh

echo "extension=mongodb.so" >> /opt/circleci/php/7.0.4/etc/php.ini
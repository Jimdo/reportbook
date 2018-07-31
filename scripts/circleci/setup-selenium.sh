#!/bin/sh

docker run -d -p 4444:4444 --name selenium-hub selenium/hub:2.53.0

docker run -d --link selenium-hub:hub selenium/node-chrome-debug:2.53.0

docker run -d --link selenium-hub:hub selenium/node-firefox-debug:2.53.0
#!/bin/sh

set -e

env

echo $PWD

docker run --name reportbook-mongodb \
    -v reportbook-data:/data/db \
    -v $TRAVIS_BUILD_DIR/scripts:/scripts \
    -p 27017:27017 \
    -d mongo --auth

docker exec reportbook-mongodb /scripts/setup-mongo-server.sh

docker ps

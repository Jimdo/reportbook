#!/bin/sh

set -e

docker run --name reportbook-mongodb \
    -v reportbook-data:/data/db \
    -v $TRAVIS_BUILD_DIR/scripts:/scripts \
    -p 27017:27017 \
    -d mongo --auth

# let the MongoDB server start up...
echo "Waiting 2 seconds for the MongoDB server..."
sleep 2

docker exec reportbook-mongodb /scripts/setup-mongo-server.sh

#!/bin/sh

set -e

pwd 

docker run --name reportbook-mongodb \
    -v reportbook-data:/data/db \
    -v /home/ubuntu/reportbook/scripts:/scripts \
    -p $MONGO_PORT:27017 \
    -d mongo --auth

# let the MongoDB server start up...
echo "Waiting 2 seconds for the MongoDB server..."
sleep 2

sudo lxc-attach -n "$(docker inspect --format "{{.Id}}" reportbook-mongodb)" -- bash -c docker exec

docker exec reportbook-mongodb /scripts/setup-mongo-server.sh

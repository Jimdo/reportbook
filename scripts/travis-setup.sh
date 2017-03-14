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

docker ps

# Set port to 3307 due to:
# "docker: Error response from daemon: driver failed programming external
# connectivity on endpoint mysql: Error starting userland proxy:
# listen tcp 0.0.0.0:3306: bind: address already in use.
docker run --name mysql \
    -p 3307:3306 \
    -v $TRAVIS_BUILD_DIR/scripts:/scripts \
    -e MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD \
    -e MYSQL_DATABASE=$MYSQL_DATABASE \
    -e MYSQL_USER=$MYSQL_USER \
    -e MYSQL_PASSWORD=$MYSQL_PASSWORD \
    -d mysql

# let the MySQL server start up...
echo "Waiting 2 seconds for the MySQL server..."
sleep 2

docker ps

docker exec mysql /scripts/setup-mysql-server.sh

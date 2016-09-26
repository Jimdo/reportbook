#!/bin/sh

eval $(docker-machine env)
docker exec reportbook-mongodb /scripts/setup-mongo-server.sh

#!/bin/sh

eval $(docker-machine env)
docker exec reportbook_mongo_1 /scripts/setup-mongo-server.sh

#!/bin/sh

eval $(docker-machine env)
docker run --rm --name reportbook-mongodb -v reportbook-data:/data/db -p 27017:27017 mongo

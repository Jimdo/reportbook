#!/bin/sh

eval $(docker-machine env)
docker pull $1

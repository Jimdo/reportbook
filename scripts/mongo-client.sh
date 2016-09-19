#!/bin/sh

eval $(docker-machine env)
mongo $(docker-machine ip)

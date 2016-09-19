#!/bin/sh

MONGO_SERVER_IP=$(docker-machine ip) scripts/phpunit

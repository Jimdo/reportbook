#!/bin/sh

export APPLICATION_ENV=test

export DOCKER_IP=$(ip addr show docker0 | grep "inet\b" | awk '{print $2}' | cut -d/ -f1)

./scripts/phpunit

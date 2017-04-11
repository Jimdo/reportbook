#!/bin/sh

export APPLICATION_ENV=test

export REPORTBOOK_IP=$(ip addr show docker0 | grep "inet\b" | awk '{print $2}' | cut -d/ -f1)

./scripts/phpunit

#!/bin/sh

export APPLICATION_ENV=test

export MONGO_HOST=$(docker-machine ip)

scripts/phpbench run benchmarks --report=default

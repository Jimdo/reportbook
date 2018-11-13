#!/bin/sh

export APPLICATION_ENV=test

export MONGO_HOST=localhost

scripts/phpbench run benchmarks --report=default

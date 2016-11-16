#!/bin/sh

export APPLICATION_ENV=dev

export MONGO_HOST=$(docker-machine ip)

scripts/phpbench run benchmarks/data_retrieval_approaches.php --report=default

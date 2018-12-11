#!/bin/sh

if [ -z ${MONGO_DATABASE+x} ]; then
  MONGO_DATABASE=reportbook_$APPLICATION_ENV
fi

# Create unique indices
mongo --host mongo:27017 ${MONGO_DATABASE} < scripts/mongo/create-indices.js

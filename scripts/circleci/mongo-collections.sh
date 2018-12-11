#!/bin/sh

if [ -z ${MONGO_DATABASE+x} ]; then
  MONGO_DATABASE=reportbook_$APPLICATION_ENV
fi

# Create collections
mongo --host mongodb://mongo:27017 ${MONGO_DATABASE} < scripts/mongo/create-collections.js

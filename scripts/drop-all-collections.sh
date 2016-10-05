#!/bin/sh

if [ -z ${APPLICATION_ENV+x} ]; then
  APPLICATION_ENV=dev
fi

if [ -z ${MONGO_USERNAME+x} ]; then
  MONGO_USERNAME=reportbook-$APPLICATION_ENV
fi

if [ -z ${MONGO_PASSWORD+x} ]; then
  MONGO_PASSWORD=geheim
fi

if [ -z ${MONGO_DATABASE+x} ]; then
  MONGO_DATABASE=reportbook_$APPLICATION_ENV
fi

eval $(docker-machine env)
sed "s/DATABASE/${MONGO_DATABASE}/g" scripts/mongo/drop-all-collections.js\
    | mongo $(docker-machine ip)/${MONGO_DATABASE} \
      -u $MONGO_USERNAME -p $MONGO_PASSWORD

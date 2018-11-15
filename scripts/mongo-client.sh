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

mongo localhost/$MONGO_DATABASE \
    -u $MONGO_USERNAME -p $MONGO_PASSWORD

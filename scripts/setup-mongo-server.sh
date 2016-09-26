#!/bin/sh

set -e

if [ -z ${MONGO_ADMIN_PASSWORD+x} ]; then
  MONGO_ADMIN_PASSWORD=geheim
fi

if [ -z ${MONGO_USERNAME+x} ]; then
  MONGO_USERNAME=reportbook
fi

if [ -z ${MONGO_PASSWORD+x} ]; then
  MONGO_PASSWORD=geheim
fi

if [ -z ${MONGO_DATABASE+x} ]; then
  MONGO_DATABASE=reportbook
fi

sed "s/PASSWORD/$MONGO_ADMIN_PASSWORD/g" /scripts/mongo/create-admin-user.js | mongo admin

sed "s/USERNAME/$MONGO_USERNAME/g" /scripts/mongo/create-user.js \
    | sed "s/PASSWORD/$MONGO_PASSWORD/g" \
    | sed "s/DATABASE/$MONGO_DATABASE/g" \
    | mongo admin -u admin -p $MONGO_ADMIN_PASSWORD

#!/bin/sh

set -e

pwd 

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

# Create admin user
sed "s/PASSWORD/$MONGO_ADMIN_PASSWORD/g" /home/ubuntu/reportbook/scripts/mongo/create-admin-user.js | mongo admin

# Create dev user
sed "s/USERNAME/$MONGO_USERNAME-dev/g" /home/ubuntu/reportbook/scripts/mongo/create-user.js \
    | sed "s/PASSWORD/$MONGO_PASSWORD/g" \
    | sed "s/DATABASE/${MONGO_DATABASE}_dev/g" \
    | mongo admin -u admin -p $MONGO_ADMIN_PASSWORD

# Create test user
sed "s/USERNAME/$MONGO_USERNAME-test/g" /home/ubuntu/reportbook/scripts/mongo/create-user.js \
    | sed "s/PASSWORD/$MONGO_PASSWORD/g" \
    | sed "s/DATABASE/${MONGO_DATABASE}_test/g" \
    | mongo admin -u admin -p $MONGO_ADMIN_PASSWORD

# Create collections
/home/ubuntu/reportbook/scripts/mongo-collections.sh

# Create unique indices
/home/ubuntu/reportbook/scripts/mongo-indices.sh

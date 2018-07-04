#!/bin/sh

set -e

MONGO_SERVER=$1
MONGO_PORT=$2
MONGO_DB=$3
MONGO_USER=$4

if [ -z ${MONGO_PASSWORD+x} ]; then
  echo "MONGO_PASSWORD not set!"
  exit
fi

if [ -z ${AWS_ACCESS_KEY_ID+x} ]; then
  echo "AWS_ACCESS_KEY_ID not set!"
  exit
fi

if [ -z ${AWS_SECRET_ACCESS_KEY+x} ]; then
  echo "AWS_SECRET_ACCESS_KEY not set!"
  exit
fi

BACKUP=$(date '+reportbook_%Y%m%d_%H%M%S')
mongodump \
  --host=$MONGO_SERVER \
  --port=$MONGO_PORT \
  --db=$MONGO_DB \
  --excludeCollection sessions \
  -u $MONGO_USER \
  -p $MONGO_PASSWORD \
  --out $BACKUP

aws s3 cp $BACKUP s3://azubi-reportbook-storage/$BACKUP --recursive

#!/bin/sh

set -e

SERVER=$1
PORT=$2
DB=$3
USER=$4
PASS=$5

BACKUP=$(date '+reportbook_%Y%m%d_%k%M%S')
mongodump \
  --host=$SERVER \
  --port=$PORT \
  --db=$DB \
  --excludeCollection sessions \
  -u $USER \
  -p $PASS \
  --out $BACKUP

AWS_ACCESS_KEY_ID=$6 AWS_SECRET_ACCESS_KEY=$7 aws s3 cp $BACKUP s3://azubi-reportbook-storage/$BACKUP --recursive


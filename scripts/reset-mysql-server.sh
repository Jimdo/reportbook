#!/bin/sh

set -e

if [ -e .env ]
then
    source .env
fi

eval MYSQL_DATABASE=$MYSQL_DATABASE
eval MYSQL_USER=$MYSQL_USER
eval MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD

# drop all
sed "s/MYSQL_USER/${MYSQL_USER}dev/g" scripts/mysql/drop-all.sql \
    | sed "s/MYSQL_DATABASE/${MYSQL_DATABASE}dev/g" \
    | mysql -p$MYSQL_ROOT_PASSWORD

# drop all
sed "s/MYSQL_USER/${MYSQL_USER}test/g" scripts/mysql/drop-all.sql \
    | sed "s/MYSQL_DATABASE/${MYSQL_DATABASE}test/g" \
    | mysql -p$MYSQL_ROOT_PASSWORD

#!/bin/sh

set -e
set -x

if [ -e .env ]
then
    source .env
fi

eval MYSQL_DATABASE=$MYSQL_DATABASE
eval MYSQL_USER=$MYSQL_USER
eval MYSQL_PASSWORD=$MYSQL_PASSWORD
eval MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD

if [ -z ${MYSQL_PORT+x} ]; then
  MYSQL_PORT=3306
fi

# Create database
sed "s/MYSQL_DATABASE/${MYSQL_DATABASE}dev/g" scripts/mysql/create-database.sql | mysql -p$MYSQL_ROOT_PASSWORD -h 127.0.0.1 -P$MYSQL_PORT

# Create user
sed "s/MYSQL_USER/${MYSQL_USER}dev/g" scripts/mysql/create-user.sql \
    | sed "s/MYSQL_PASSWORD/$MYSQL_PASSWORD/g" \
    | sed "s/MYSQL_DATABASE/${MYSQL_DATABASE}dev/g" \
    | mysql -p$MYSQL_ROOT_PASSWORD -h 127.0.0.1 -P$MYSQL_PORT

# Create tables from database dump
 mysql -p$MYSQL_ROOT_PASSWORD ${MYSQL_DATABASE}dev -h 127.0.0.1 -P$MYSQL_PORT < scripts/mysql/mysql-dump.sql

# Create database
sed "s/MYSQL_DATABASE/${MYSQL_DATABASE}test/g" scripts/mysql/create-database.sql | mysql -p$MYSQL_ROOT_PASSWORD -h 127.0.0.1 -P$MYSQL_PORT

# Create user
sed "s/MYSQL_USER/${MYSQL_USER}test/g" scripts/mysql/create-user.sql \
    | sed "s/MYSQL_PASSWORD/$MYSQL_PASSWORD/g" \
    | sed "s/MYSQL_DATABASE/${MYSQL_DATABASE}test/g" \
    | mysql -p$MYSQL_ROOT_PASSWORD -h 127.0.0.1 -P$MYSQL_PORT

# Create tables from database dump
 mysql -p$MYSQL_ROOT_PASSWORD ${MYSQL_DATABASE}test -h 127.0.0.1 -P$MYSQL_PORT < scripts/mysql/mysql-dump.sql

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

MYSQL_CMD="mysql -u root"

if [ $MYSQL_ROOT_PASSWORD != travis ]; then
  MYSQL_CMD="mysql -u root -p${MYSQL_ROOT_PASSWORD}"
fi

# Create database
sed "s/MYSQL_DATABASE/${MYSQL_DATABASE}dev/g" scripts/mysql/create-database.sql | $MYSQL_CMD
# Create user
sed "s/MYSQL_USER/${MYSQL_USER}dev/g" scripts/mysql/create-user.sql \
    | sed "s/MYSQL_PASSWORD/$MYSQL_PASSWORD/g" \
    | sed "s/MYSQL_DATABASE/${MYSQL_DATABASE}dev/g" \
    | $MYSQL_CMD
# Create tables from database dump
$MYSQL_CMD ${MYSQL_DATABASE}dev < scripts/mysql/mysql-dump.sql

# Create database
sed "s/MYSQL_DATABASE/${MYSQL_DATABASE}test/g" scripts/mysql/create-database.sql | $MYSQL_CMD
# Create user
sed "s/MYSQL_USER/${MYSQL_USER}test/g" scripts/mysql/create-user.sql \
    | sed "s/MYSQL_PASSWORD/$MYSQL_PASSWORD/g" \
    | sed "s/MYSQL_DATABASE/${MYSQL_DATABASE}test/g" \
    | $MYSQL_CMD
# Create tables from database dump
$MYSQL_CMD ${MYSQL_DATABASE}test < scripts/mysql/mysql-dump.sql

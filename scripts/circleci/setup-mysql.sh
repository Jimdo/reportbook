#!/bin/sh

set -e


echo first start mysql
mysql -u root -h mysql -P 3306
echo first end mysql

mysql -u root -e 'CREATE DATABASE reportbook_test;'

mysql -u root -e "CREATE USER 'reportbook-test'@'localhost' IDENTIFIED BY 'geheim';"

mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'reportbook-test'@'localhost';"

mysql -u root -e "REVOKE CREATE USER, SUPER ON *.* FROM 'reportbook-test'@'localhost';"

mysql -u root reportbook_test < ./scripts/mysql/mysql-dump.sql

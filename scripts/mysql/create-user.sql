CREATE USER IF NOT EXISTS 'MYSQL_USER'@'%' IDENTIFIED BY 'MYSQL_PASSWORD';
GRANT ALL PRIVILEGES ON *.* TO 'MYSQL_USER'@'%' WITH GRANT OPTION;
REVOKE CREATE USER, SUPER ON *.* FROM 'MYSQL_USER'@'%';

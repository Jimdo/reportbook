FROM php:7.2-apache

MAINTAINER hauke.stange@jimdo.com

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    libpcre3-dev \
    libssl-dev \
    unzip \
    wget \
    zlib1g \
    zlib1g-dev \
    libpng-dev

RUN docker-php-ext-install zip sockets gd

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www

RUN rmdir html

COPY scripts/install-composer.sh /var/www/scripts/install-composer.sh

COPY vhost.conf /etc/apache2/sites-enabled/000-default.conf

COPY composer.json /var/www
COPY composer.lock /var/www

COPY config.yml /var/www

RUN scripts/install-composer.sh && scripts/composer install --no-dev

COPY app/ /var/www/app

COPY src/ /var/www/src

RUN chown -R www-data:www-data *

FROM php:7.0-apache

MAINTAINER hauke.stange@jimdo.com

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    libssl-dev \
    unzip \
    wget \
    zlib1g \
    zlib1g-dev

RUN docker-php-ext-install zip sockets

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY xdebug.ini /usr/local/etc/php/conf.d

WORKDIR /var/www

RUN rmdir html

COPY scripts/install-composer.sh /var/www/scripts/install-composer.sh

COPY vhost.conf /etc/apache2/sites-enabled/000-default.conf

COPY composer.json /var/www

COPY config.yml /var/www

RUN scripts/install-composer.sh && scripts/composer install --no-dev

COPY app/ /var/www/app

COPY src/ /var/www/src

RUN chown -R www-data:www-data *

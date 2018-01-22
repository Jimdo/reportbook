FROM php:7.0-apache

MAINTAINER jennifer.eschenhorn@jimdo.com & tom.stich@jimdo.com

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    libssl-dev \
    unzip \
    wget \
    zlib1g \
    zlib1g-dev

RUN docker-php-ext-install zip sockets

WORKDIR /var/www

COPY api/ /var/www/api

COPY vhost.conf /etc/apache2/sites-enabled/000-default.conf

COPY ../composer.json /var/www

COPY ../scripts/install-composer.sh /var/www/scripts/install-composer.sh

RUN rmdir html

RUN scripts/install-composer.sh && scripts/composer install --no-dev

RUN chown -R www-data:www-data *

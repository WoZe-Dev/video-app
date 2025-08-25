ARG PHP_VERSION=8.0.0

FROM php:$PHP_VERSION-fpm

ARG PHP_USER_NAME=php
ARG PHP_USER_IDENTIFIER=1000
ARG PHP_USER_SHELL=/bin/bash
ARG PHP_USER_HOME_DIRECTORY=/home/php

RUN useradd --create-home --system --home-dir $PHP_USER_HOME_DIRECTORY --shell $PHP_USER_SHELL --uid $PHP_USER_IDENTIFIER $PHP_USER_NAME

RUN docker-php-ext-install pdo_mysql

# Copier la configuration PHP pour les uploads
COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# Copier la configuration PHP pour les sessions
COPY sessions.ini /usr/local/etc/php/conf.d/sessions.ini

## Copy the startup script into the container
#COPY startup.sh /usr/local/bin/startup.sh
#RUN chmod +x /usr/local/bin/startup.sh
#
## Execute the file containing the startup script that will start the migrations process.
#CMD ["startup.sh"]

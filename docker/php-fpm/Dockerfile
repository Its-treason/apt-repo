FROM composer:2.3.7 AS composer

FROM php:8.1-fpm-buster AS base

RUN apt update && \
    apt install dpkg-dev libffi-dev gpg -y

RUN docker-php-ext-install -j$(nproc) pdo_mysql && \
    docker-php-ext-install -j$(nproc) pdo && \
    docker-php-ext-install -j$(nproc) ffi

COPY docker/php-fpm/custom.ini /usr/local/etc/php/conf.d/custom.ini


RUN chown www-data:www-data /var/www -R


USER www-data

COPY pgp-key.private /tmp/pgp-key.private

RUN cat /tmp/pgp-key.private | gpg --import

USER root

RUN rm /tmp/pgp-key.private
##

RUN mkdir /app
WORKDIR /app

FROM base AS development

ENV APP_ENV="development"

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

FROM base AS production

ENV APP_ENV="production"

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY . /app

COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN rm -rf /app/vendor
RUN composer install
RUN rm /usr/bin/composer

FROM composer:2.3 AS composer

FROM php:8.2-fpm-alpine AS base

RUN mkdir /app
WORKDIR /app

RUN apk add libffi-dev gpg gpg-agent git zip unzip dpkg-dev dpkg \
 && docker-php-ext-install -j$(nproc) pdo_mysql pdo ffi

COPY docker/php-fpm/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Change so owner of the www-data home so it can create the .gnupg dir
RUN chown www-data:www-data /var/www -R

COPY docker/php-fpm/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
CMD [ "/entrypoint.sh" ]

ARG TARGETARCH
# Latest releases available at https://github.com/aptible/supercronic/releases
ENV SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/v0.2.25/supercronic-linux-${TARGETARCH} \
    SUPERCRONIC=supercronic-linux-${TARGETARCH} \
    SUPERCRONIC_SHA1SUM=642f4f5a2b67f3400b5ea71ff24f18c0a7d77d49

RUN curl -fsSLO "$SUPERCRONIC_URL" \
 && chmod +x "$SUPERCRONIC" \
 && mv "$SUPERCRONIC" "/usr/local/bin/${SUPERCRONIC}" \
 && ln -s "/usr/local/bin/${SUPERCRONIC}" /usr/local/bin/supercronic

COPY docker/php-fpm/crontab /crontab

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

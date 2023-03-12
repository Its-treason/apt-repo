FROM nginx:1.23.0-alpine AS base

RUN mkdir /app

FROM base AS development

COPY ./docker/nginx/nginx.development.conf /etc/nginx/conf.d/default.conf

FROM base AS production

COPY public /app/public
COPY docker/nginx/nginx.production.conf /etc/nginx/conf.d/default.conf

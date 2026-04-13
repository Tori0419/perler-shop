FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
  --no-dev \
  --prefer-dist \
  --optimize-autoloader \
  --no-scripts \
  --no-interaction \
  --no-progress \
  --ignore-platform-req=php

COPY . /app

FROM node:20-slim AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.4-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    libonig-dev \
    libsqlite3-dev \
    libzip-dev \
    pkg-config \
    unzip \
    && docker-php-ext-install mbstring pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /app /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build

RUN chmod +x /var/www/html/scripts/render-start.sh

ENV APP_ENV=production \
    APP_DEBUG=false

CMD ["/var/www/html/scripts/render-start.sh"]

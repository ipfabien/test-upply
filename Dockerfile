FROM composer:2 AS composer

FROM php:8.3-cli-alpine

RUN set -eux; \
    apk add --no-cache --update --virtual .build-deps $PHPIZE_DEPS postgresql-dev curl-dev; \
    docker-php-ext-install pdo pdo_pgsql curl; \
    apk del .build-deps; \
    apk add --no-cache postgresql-libs libcurl

WORKDIR /app

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock* ./
RUN composer install --no-interaction --no-ansi --no-scripts --no-progress --prefer-dist --no-dev || true

COPY . .

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]


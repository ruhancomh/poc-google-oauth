FROM picpay/php:7.4-fpm-base

COPY application/composer.* /app/

WORKDIR /app

RUN composer install --no-autoloader

COPY application/ /app
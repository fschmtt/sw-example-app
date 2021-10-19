FROM php:8.0-fpm

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY docker/php/99-xdebug.ini "$PHP_INI_DIR/conf.d/99-xdebug.ini"

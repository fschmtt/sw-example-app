FROM php:8.1-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY docker/php/99-xdebug.ini "$PHP_INI_DIR/conf.d/99-xdebug.ini"

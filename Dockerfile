FROM php:8-fpm-alpine
RUN apk add --no-cache composer php-mysqli
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli
RUN rm -rf /var/www/*
COPY --chown=www-data:www-data ./ /var/www
COPY ./resources/php.ini $PHP_INI_DIR/php.ini
RUN composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader -d /var/www
USER 1025
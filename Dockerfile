FROM php:8.0-fpm-alpine3.14
RUN apk add --no-cache composer nginx php-mysqli
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli
COPY --chown=www-data:www-data ./ /var/www
COPY ./resources/nginx.conf /etc/nginx/nginx.conf
COPY ./resources/php.ini $PHP_INI_DIR/php.ini
RUN composer install -d /var/www
CMD php-fpm -D; nginx; tail -F /dev/null;

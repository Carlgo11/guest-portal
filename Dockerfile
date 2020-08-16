FROM alpine AS jekyll

###
# Jekyll
###

# Install Jekyll dependencies
RUN apk add --no-cache build-base libxml2-dev libxslt-dev ruby-full ruby-dev gcc linux-headers

# Create www user
RUN adduser -D -u 1000 -h /tmp/www www

# Copy Jekyll files
COPY --chown=www:www jekyll /tmp/jekyll
WORKDIR /tmp/jekyll

USER www

# Install gems
RUN bundle config build.nokogiri --use-system-libraries
RUN bundle config set path 'vendor/bundle'
RUN bundle install

# Build site
RUN bundle exec jekyll build

USER root
RUN mv _site /opt/www/

# Remove Jekyll dependencies
RUN apk del build-base libxml2-dev libxslt-dev ruby-full ruby-dev gcc linux-headers

FROM alpine

###
# PHP
###

# Install PHP dependencies
RUN apk add --no-cache php-fpm php-curl php-json php-session composer

# Create www user
RUN adduser -D -u 1000 -h /tmp/www www

# Set up PHP-FPM
COPY php-install.sh /
RUN chmod +x /php-install.sh; /bin/sh /php-install.sh; rm /php-install.sh

# Copy PHP files
COPY ./php /opt/php
WORKDIR /opt/php

# Install libraries
RUN composer install --no-dev

RUN apk del composer

###
# Nginx
###

# Install Nginx
RUN apk add --no-cache nginx 

# Copy Nginx virtual server config
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# Bug fixes
RUN mkdir /run/nginx
RUN sed -i 's/cgi.fix_pathinfo= 0/cgi.fix_pathinfo=1/g' /etc/php7/php.ini

###
# Default Environment variables
###

ENV UNIFI_SITE=default
ENV UNIFI_VERSION=5.13.32

###
# Execution
###

COPY --chown=1000 --from=jekyll /opt/www/ /opt/www/
ONBUILD RUN chown www:www /opt/www/ -R
EXPOSE 80
CMD php-fpm7 && nginx; tail -F /var/log/nginx/error.log
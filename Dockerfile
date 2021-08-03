FROM webdevops/php-nginx:8.0-alpine
COPY ./ /app
RUN composer install -d /app
RUN echo "variables_order = \"EGPCS\"" >> /opt/docker/etc/php/php.ini
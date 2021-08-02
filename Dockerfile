FROM webdevops/php-nginx:7.4-alpine
COPY ./ /app
RUN composer install -d /app
RUN echo "variables_order = \"EGPCS\"" >> /opt/docker/etc/php/php.ini
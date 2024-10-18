FROM composer:latest AS builder

WORKDIR /app

COPY ./src/composer.json ./src/composer.lock ./

RUN composer install --no-dev --optimize-autoloader

FROM php:8.2-apache

ARG TMZN=Europe/Berlin
ARG APP_DOMAIN

WORKDIR /var/www/html/word-app/api

# set time zone
RUN apt-get update && apt-get install -y vim libmariadb-dev tzdata
RUN ln -snf /usr/share/zoneinfo/$TMZN /etc/localtime && echo $TMZN > /etc/timezone

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod headers
RUN echo -e "<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin \"*\"
    Header set Access-Control-Allow-Methods \"GET, POST, PUT, DELETE, OPTIONS\"
    Header set Access-Control-Allow-Headers \"Content-Type, Authorization\"
    Header set Access-Control-Allow-Credentials \"true\"
</IfModule>" >> /etc/apache2/apache2.conf
# enable .htaccess file to rewrite urls
RUN a2enmod rewrite
RUN echo "ServerName $APP_DOMAIN" >> /etc/apache2/conf-available/servername.conf && \
    a2enconf servername

RUN mkdir logs && chmod 777 ./logs

COPY . .
COPY --from=builder /app/vendor ./src/vendor

EXPOSE 80

ENTRYPOINT ["apache2-foreground"]
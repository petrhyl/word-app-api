FROM composer:latest AS builder

WORKDIR /app

COPY ./src/composer.json ./src/composer.lock ./

RUN composer install --no-dev --optimize-autoloader

FROM php:8.2-apache

ARG TMZN=Europe/Berlin
ARG APP_DOMAIN

WORKDIR /var/www/html/blog/api

# set time zone
RUN apt-get update && apt-get install -y vim libmariadb-dev tzdata
RUN ln -snf /usr/share/zoneinfo/$TMZN /etc/localtime && echo $TMZN > /etc/timezone

RUN docker-php-ext-install pdo pdo_mysql

# enable .htaccess file to rewrite urls
RUN a2enmod rewrite
RUN echo "ServerName $APP_DOMAIN" >> /etc/apache2/conf-available/servername.conf && \
    a2enconf servername

RUN mkdir log && chmod 777 ./log

COPY . .
COPY --from=builder /app/vendor ./src/vendor

EXPOSE 80

ENTRYPOINT ["apache2-foreground"]
FROM php:7.2-apache AS base

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libbz2-dev \
    libpng-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libreadline-dev \
    libfreetype6-dev 

RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite headers

# supporting custom apache config
COPY .system/apache.d/000-default.conf /etc/apache2/sites-available/000-default.conf

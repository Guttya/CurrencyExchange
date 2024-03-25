FROM php:8.3-fpm

RUN apt-get update \
    && apt-get install -y unzip git

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# php-pgsql
RUN apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/app
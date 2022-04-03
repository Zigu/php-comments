FROM php:7.1-apache

RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update && apt-get upgrade -y
RUN a2enmod rewrite

ENV TZ=Europe/Berlin
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "${TZ}"\n' > /usr/local/etc/php/conf.d/tzone.ini

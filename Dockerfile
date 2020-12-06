FROM php:7.1-apache

# Install php extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY vhost.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite
RUN service apache2 restart


FROM php:7.1-apache

# Install php extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable apache and restart
RUN a2enmod rewrite
RUN service apache2 restart

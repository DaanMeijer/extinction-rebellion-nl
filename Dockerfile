FROM php:7.2-apache

# Install node and npm
RUN apt-get update -y && apt-get install nodejs npm git zip unzip -y

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install php extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable apache and restart
RUN a2enmod rewrite
RUN service apache2 restart

COPY . /var/www/html

# Apache conf
ADD vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

RUN composer install

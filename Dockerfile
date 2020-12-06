FROM php:7.1-apache

# Install php extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN apt update && apt install -y npm 

COPY vhost.conf /etc/apache2/sites-available/000-default.conf
COPY web /var/www/html/web
COPY config /var/www/html/config
COPY .env.example /var/www/html/.env
COPY wp-cli.yml /var/www/html/wp-cli.yml
COPY composer.json /var/www/html/composer.json
COPY composer.lock /var/www/html/composer.lock
COPY package-lock.json /var/www/html/package-lock.json


RUN curl -SL0 https://cloud.extinctionrebellion.nl/index.php/s/QCENwJwpbCoqoNB/download -o plugins.tar.gz && tar -xvf plugins.tar.gz -C web/app/plugins/ && rm plugins.tar.gz
RUN cd web/app/themes/xrnl && npm install

# Enable apache and restart
RUN a2enmod rewrite
CMD service apache2 restart


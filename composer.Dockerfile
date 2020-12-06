FROM composer

WORKDIR /var/www/html

volumes:
    - plugins:/var/www/html
command: install
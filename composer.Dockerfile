FROM composer

WORKDIR /var/www/html

RUN apk add --update npm

COPY html /var/www/html

RUN curl -SL0 https://cloud.extinctionrebellion.nl/index.php/s/QCENwJwpbCoqoNB/download -o plugins.tar.gz && tar -xvf plugins.tar.gz -C web/app/plugins/ && rm plugins.tar.gz
RUN cd web/app/themes/xrnl && npm install

CMD install
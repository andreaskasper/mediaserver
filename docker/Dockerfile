FROM php:8-apache

RUN apt-get update \
    && mkdir -p --mode=777 /inbox \
    && mkdir -p --mode=777 /converted \
    && mkdir -p --mode=777 /originals \
    && mkdir -p --mode=777 /config \
    && chown -R www-data /config \
    && chown -R www-data /converted \
    && chown -R www-data /inbox \
    && chown -R www-data /originals \
    && chgrp -R www-data /config \
    && chgrp -R www-data /converted \
    && chgrp -R www-data /inbox \
    && chgrp -R www-data /originals \
    && apt-get install -y ffmpeg libapache2-mod-xsendfile sqlite3 wget \
    && a2enmod xsendfile rewrite \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean

ADD 000-default.conf /etc/apache2/sites-enabled/000-default.conf
ADD docker-entrypoint /usr/local/bin/
ADD uploads.ini /usr/local/etc/php/conf.d/uploads.ini

#VOLUME /inbox
#VOLUME /converted
#VOLUME /originals
#VOLUME /config

ENV DOCUMENT_ROOT=/var/www/html/public_html/

ENTRYPOINT ["docker-entrypoint"]

WORKDIR /var/www/html

EXPOSE 80
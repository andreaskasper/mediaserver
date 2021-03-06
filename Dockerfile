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
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
    && a2enmod xsendfile rewrite \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean

ADD docker/000-default.conf /etc/apache2/sites-enabled/000-default.conf
ADD docker/docker-entrypoint /usr/local/bin/
ADD docker/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
ADD docker/initd_phpconverter /etc/init.d/phpconverter
ADD src /var/www/html

#VOLUME /inbox
#VOLUME /converted
#VOLUME /originals
#VOLUME /config

ENV DOCUMENT_ROOT=/var/www/html/public_html/

ENTRYPOINT ["docker-entrypoint"]
CMD ["apache2-foreground"]

WORKDIR /var/www/html

EXPOSE 80
FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    git \
    apache2 \
    php \
    libapache2-mod-php \
    php-zip \
    php-xml \
    php-gd \
    php-mbstring \
    php-intl \
    curl \
    unzip \
    openssl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/apache-selfsigned.key \
    -out /etc/ssl/certs/apache-selfsigned.crt \
    -subj "/C=ES/ST=Valencia/L=Valencia/O=ProyectoCalidad/CN=localhost"

RUN a2enmod ssl rewrite headers

ARG REPO_URL="https://github.com/QiweiLiuYang/Proyecto-Calidad"

RUN rm -rf /var/www/html/* \
    && git clone ${REPO_URL} /tmp/repo \
    && mv /tmp/repo/www/* /var/www/html/ \
    && rm -rf /tmp/repo

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN cd /var/www/html/php && composer install --no-dev --optimize-autoloader

COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

RUN mkdir -p /var/www/html/php/actas \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/php/actas

EXPOSE 80 443

CMD ["apachectl", "-D", "FOREGROUND"]
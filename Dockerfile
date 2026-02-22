FROM ubuntu:24:04

ENV DEBIAN_FRONTEND=nointeractive

RUN apt-get update && apt-get install -y \
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
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
RUN a2enmod rewrite \
    && rm /var/www/html/index.html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY www/ /var/www/html/

RUN cd php && composer install --no-dev --optimize-autoloader

RUN mkdir -p php/actas \
    && chown -R www-data:www-data php/actas \
    && chmod -R 775 php/actas

EXPOSE 80

CMD ["apachectl", "-D", "FOREGROUND"]
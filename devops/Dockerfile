FROM php:7.2-fpm-alpine

RUN apk update && apk add --no-cache \
    libmcrypt-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-turbo \
    libpng-dev \
    libwebp-dev \
    libsodium-dev \
    zlib-dev \
    icu-dev \
    git \
    zip \
    g++ \
    vim \
    jq \
    supervisor \
    nginx \
    freetype \
    libpng \
    libjpeg-turbo \
    freetype-dev \
    libpng-dev \
    jpeg-dev \
    libjpeg \
    libjpeg-turbo-dev

RUN apk update && docker-php-ext-install iconv \
        pdo \
        pdo_mysql \
        zip \
        exif \
        sodium \
    && docker-php-ext-install mbstring \
    && docker-php-ext-configure gd \
    --with-freetype-dir=/usr/lib/ \
    --with-png-dir=/usr/lib/ \
    --with-jpeg-dir=/usr/lib/ \
    --with-gd \
    && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN apk add --update --no-cache autoconf g++ make \
&& pecl install redis \
&&  docker-php-ext-enable redis

# ENV SWOOLE_VERSION=4.3.4

#RUN set -xe && \
#        pecl install swoole-${SWOOLE_VERSION} && \
#        docker-php-ext-enable swoole

#ADD ./devops/supervisord.conf /etc/supervisor/supervisord.conf
#ADD ./devops/http-worker.conf /etc/supervisor/conf.d/http.conf
COPY ./devops/nginx.conf /etc/nginx/nginx.conf
COPY ./devops/default-nginx.conf /etc/nginx/conf.d/default.conf

COPY . /var/www/app
WORKDIR /var/www/app

RUN chmod -R 0777 ./storage

RUN chmod +x ./devops/init.sh

RUN curl -sS https://getcomposer.org/installer | \
php -- --install-dir=/usr/bin/ --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev

ENTRYPOINT ["./devops/init.sh"]

ARG IMAGE_VERSION=8.3

FROM php:$IMAGE_VERSION AS edm-php
ENV TZ=Europe/Berlin
ENV DEBIAN_FRONTEND=noninteractive
ENV PIP_ROOT_USER_ACTION=ignore

ARG DOCKER_USER_ID=1000
ARG DOCKER_USER_NAME=www-data
ARG DOCKER_GROUP_ID=1000
ARG DOCKER_GROUP_NAME=www-data

RUN \
  getent group $DOCKER_GROUP_NAME || groupadd -g $DOCKER_GROUP_ID $DOCKER_GROUP_NAME && \
  getent user $DOCKER_USER_NAME || useradd -u $DOCKER_USER_ID $DOCKER_USER_NAME -g $DOCKER_GROUP_NAME && \
  mkhomedir_helper $DOCKER_USER_NAME && \
  echo "deb http://http.us.debian.org/debian testing non-free contrib main" > /etc/apt/sources.list && \
  apt-get update && \
  apt-get install -y sudo iproute2 htop mc wget git && \
  apt-get clean all

RUN apt-get update && \
    apt-get install -y \
        libpcre3-dev \
        zlib1g-dev \
        libfreetype-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libicu-dev \
        libzip-dev \
        libxml2-dev \
    && \
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ --with-webp && \
    docker-php-ext-install exif gd intl pdo_mysql zip soap && \
    apt-get clean all

RUN pecl install oauth && \
	docker-php-ext-enable oauth

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash && \
    apt-get install symfony-cli

COPY conf.d/00-default.ini $PHP_INI_DIR/conf.d/00-default.ini

RUN if test -f /usr/local/etc/php-fpm.d/docker.conf; then echo "access.log = /dev/null" >> /usr/local/etc/php-fpm.d/docker.conf; fi

RUN mkdir -p /app
RUN chown -R $DOCKER_USER_NAME:$DOCKER_GROUP_NAME /app/

RUN git config --system user.email "edm@1221systems.com" && \
    git config --system user.name "1221 EDM"

WORKDIR /app

USER $DOCKER_USER_NAME

FROM edm-php AS edm-php-cron

USER root

RUN apt-get update && \
    apt-get install -y \
        cronie \
    && \
    apt-get clean all

CMD ["crond", "-f"]

FROM edm-php AS edm-php-dev

USER root

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

COPY conf.d/20-xdebug.ini $PHP_INI_DIR/conf.d/20-xdebug.ini

USER $DOCKER_USER_NAME
FROM php:7.4-fpm

ARG USER_ID
ARG GROUP_ID

RUN getent group $GROUP_ID || addgroup  --gid $GROUP_ID user
RUN adduser --disabled-password --gecos '' --uid $USER_ID --gid $GROUP_ID user

RUN apt-get update && apt-get install -y libpq-dev libmcrypt-dev libonig-dev \
    libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-install \
        bcmath \
        mbstring \
        json \
        pgsql \
        pdo_pgsql \
        sockets \
    && docker-php-ext-enable \
        imagick \
        bcmath \
        mbstring \
        json \
        pdo_pgsql \
        pgsql \
        sockets

# install composer
RUN apt-get install -y --no-install-recommends git zip unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /usr/share/man/man1/
RUN mkdir -p /usr/share/man/man7/

USER user

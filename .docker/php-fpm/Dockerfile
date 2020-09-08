FROM php:7.4.8-fpm

RUN apt update

RUN apt install libpq-dev -y

RUN apt-get install -qq git curl libmcrypt-dev libjpeg-dev libpng-dev libfreetype6-dev libbz2-dev libzip-dev

RUN apt-get clean

RUN curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get install -y libonig-dev libicu-dev && \
    docker-php-ext-install -j$(nproc) intl

RUN curl --location --output /usr/local/bin/mhsendmail https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64 && \
    chmod +x /usr/local/bin/mhsendmail

RUN echo 'sendmail_path="/usr/local/bin/mhsendmail --smtp-addr=mailhog:1025 --from=no-reply@docker"' > /usr/local/etc/php/conf.d/sendmail.ini

RUN docker-php-ext-install pdo_mysql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY .docker/php-fpm/xdebug-dev.ini /usr/local/etc/php/conf.d/xdebug-dev.ini

COPY . /src/

WORKDIR /src/

RUN chown -R www-data:www-data /src

RUN usermod -u 1000 www-data && \
    groupmod -g 1000 www-data

FROM 'php:fpm-alpine'

RUN apk add --no-cache \
        curl \
        icu \
        rabbitmq-c-dev \
        oniguruma-dev \
        yaml-dev

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp opcache pcntl mbstring pdo pdo_mysql

COPY php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
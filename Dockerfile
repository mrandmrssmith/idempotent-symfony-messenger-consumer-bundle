ARG BASE_IMAGE=php:7.2-cli-alpine3.12

FROM ${BASE_IMAGE}

LABEL maintainer="Jack Wilson <jack.wilson@smithhotels.com>"

RUN mkdir -p /var/www/html

WORKDIR /var/www/html

COPY ./ .

ARG XDEBUG_VERSION=3.1.6

RUN apk update && apk add --no-cache zip bash curl git libxml2-dev linux-headers \
    pcre-dev ${PHPIZE_DEPS} \
    && pecl install xdebug-${XDEBUG_VERSION} \
    && rm -rf /tmp/pear \
    && apk del bash pcre-dev ${PHPIZE_DEPS} \
    && docker-php-ext-enable xdebug

#RUN curl -sS https://getcomposer.org/installer | php \
#    && mv composer.phar /usr/local/bin/composer \
#    && composer config --global repo.packagist composer https://packagist.org

ENV COMPOSER_ALLOW_SUPERUSER=1

CMD ["php", "-a"]

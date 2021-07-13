FROM php:7.4-cli

RUN apt-get update -y \
    && apt-get install -y libbz2-dev libxml2-dev \
    && docker-php-ext-install simplexml

ENTRYPOINT [ "php", "/app/bin/console" ]
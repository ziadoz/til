FROM php:8.1-fpm-alpine

ARG TIDEWAYS_EXTENSION_VERSION=5.5.12
ARG TIDEWAYS_PHP_VERSION=8.0
ARG TIDEWAYS_PHP_EXTVER=20200930

RUN wget "https://tideways.s3-eu-west-1.amazonaws.com/extension/${TIDEWAYS_EXTENSION_VERSION}/tideways-php-${TIDEWAYS_EXTENSION_VERSION}-alpine-arm64.tar.gz" && \
  tar xvzf tideways-php-${TIDEWAYS_EXTENSION_VERSION}-alpine-arm64.tar.gz && \
  cp ./build/dist/tideways-php-alpine-${TIDEWAYS_PHP_VERSION}.so /usr/local/lib/php/extensions/no-debug-non-zts-${TIDEWAYS_PHP_EXTVER}/tideways.so && \
  rm -rf tideways-php-${TIDEWAYS_EXTENSION_VERSION}-alpine-arm64.tar.gz ./build
  
COPY tideways.ini "$PHP_INI_DIR/conf.d/40-tideways.ini"
FROM php:8.1-fpm-alpine
ARG PHP_EXT="pdo pdo_mysql opcache intl mbstring"

ENV UID=1000
ENV GID=1000

WORKDIR /tmp

ADD php.ini /usr/local/etc/php/php.ini
ADD php-fpm.conf /etc/php/php-fpm.d/

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

RUN apk --update --no-cache add \
  bash \
  npm \
  acl \
  fcgi \
  file \
  gettext \
  git \
  libintl \
  icu-dev \
  zlib-dev \
  libzip-dev \
  libxml2-dev \
  oniguruma-dev 
RUN docker-php-ext-configure intl \
  && docker-php-ext-install $PHP_EXT \
  && docker-php-ext-enable $PHP_EXT

RUN curl -o /usr/local/bin/composer https://getcomposer.org/download/latest-stable/composer.phar \
  && chmod +x /usr/local/bin/composer
  
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash \
    && apk add symfony-cli

WORKDIR /var/www

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm", "-F"]
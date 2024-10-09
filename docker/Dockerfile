FROM php:8.1-apache

ARG ENV=production
ARG PHP_TZ=UTC

RUN apt-get update && \
apt-get upgrade -y && \
apt-get install -y curl libpq-dev libzip-dev sqlite3 unzip && \
docker-php-ext-install pgsql pdo_pgsql pdo_mysql gettext zip

COPY ../ /var/www/html/

# Install Composer binary
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN /usr/bin/composer install --no-dev

RUN mv "$PHP_INI_DIR/php.ini-${ENV}" "$PHP_INI_DIR/php.ini"

# Set PHP date.timezone to UTC
RUN sed -ri -e "s!;date.timezone =!date.timezone = ${PHP_TZ}!g" "$PHP_INI_DIR/php.ini"

COPY docker/config/apache-vhost.config /etc/apache2/sites-enabled/000-default.conf

# locales
RUN apt-get install -y gettext locales && \
    echo 'be_BY.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'ca_ES.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'de_DE.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'es_ES.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'en_US.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'en_GB.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'fr_FR.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'it_IT.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'ja_JP.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'nl_NL.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'nn_NO.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'pl_PL.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'pt_BR.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'ro_RO.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'ru_RU.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'sv_SE.UTF-8 UTF-8' >> /etc/locale.gen && \
    echo 'zh_CN.UTF-8 UTF-8' >> /etc/locale.gen && locale-gen

RUN apt-get remove -y libpq-dev libzip-dev

ARG user_id=1000

RUN usermod -u $user_id www-data
RUN chown -v www-data /var/www/html/application/views/cache
RUN a2enmod rewrite

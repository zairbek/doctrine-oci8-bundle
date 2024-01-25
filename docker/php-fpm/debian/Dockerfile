FROM php:8.2-fpm

###########################################################################
# Base:
###########################################################################
RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    pecl channel-update pecl.php.net; \
    apt-get install -y --no-install-recommends $PHPIZE_DEPS  zip unzip libzip-dev wget  \
    && docker-php-ext-configure zip \
    && docker-php-ext-install \
        zip \
        bcmath \
        exif \
        opcache


###########################################################################
# INTL:
###########################################################################

ARG INSTALL_INTL=true

RUN if [ ${INSTALL_INTL} = true ]; then \
    apt-get install -y libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
;fi


###########################################################################
# NODE 12:
###########################################################################

ARG INSTALL_NODEJS=false

RUN if [ ${INSTALL_NODEJS} = true ]; then \
    curl -sL https://deb.nodesource.com/setup_14.x | bash - \
    && apt-get install -yqq nodejs \
;fi

###########################################################################
# PHP REDIS EXTENSION
###########################################################################

ARG INSTALL_PHPREDIS=false

RUN if [ ${INSTALL_PHPREDIS} = true ]; then \
    # Install Php Redis Extension
    pecl install -o -f redis-5.3.4 \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis \
;fi

###########################################################################
# Mysqli Modifications:
###########################################################################

ARG INSTALL_MYSQLI=false

RUN if [ ${INSTALL_MYSQLI} = true ]; then \
    docker-php-ext-install pdo_mysql mysqli \
;fi


###########################################################################
# Postgres:
###########################################################################

ARG INSTALL_POSTGRES=true

RUN if [ ${INSTALL_POSTGRES} = true ]; then \
    apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql \
;fi


###########################################################################
# Image optimizers:
###########################################################################

USER root

ARG INSTALL_IMAGE_OPTIMIZERS=false

RUN if [ ${INSTALL_IMAGE_OPTIMIZERS} = true ]; then \
    apt-get install -y jpegoptim optipng pngquant gifsicle \
    && npm install -g svgo \
;fi

###########################################################################
# ImageMagick:
###########################################################################

USER root

ARG INSTALL_IMAGEMAGICK=false

RUN if [ ${INSTALL_IMAGEMAGICK} = true ]; then \
    apt-get install -y libmagickwand-dev imagemagick && \
    pecl install imagick && \
    docker-php-ext-enable imagick \
;fi


###########################################################################
# GD:
###########################################################################

USER root

ARG INSTALL_GD=false

RUN if [ ${INSTALL_GD} = true ]; then \
    apt-get install -yqq \
            libpng-dev \
            libwebp-dev \
            libjpeg-dev \
            libfreetype6-dev \
        && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
        && docker-php-ext-install gd \
;fi


###########################################################################
# MySQL Client:
###########################################################################

USER root

ARG INSTALL_MYSQL_CLIENT=false

RUN if [ ${INSTALL_MYSQL_CLIENT} = true ]; then \
      apt-get -y install default-mysql-client \
;fi

###########################################################################
# Composer:
###########################################################################

ARG INSTALL_COMPOSER=true

RUN if [ ${INSTALL_COMPOSER} = true ]; then \
    curl -sS https://getcomposer.org/installer | php -- \
            --install-dir=/usr/local/bin \
            --filename=composer \
       && chmod +x /usr/local/bin/composer \
;fi


###########################################################################
# xDebug:
###########################################################################

ARG INSTALL_XDEBUG=false

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    pecl install xdebug-3.1.2; \
    docker-php-ext-enable xdebug \
;fi

# Copy xdebug configuration for remote debugging
COPY docker/php-fpm/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini


###########################################################################
# Oracle OCI8 + pdo_oci:
###########################################################################

WORKDIR /tmp
ENV LD_LIBRARY_PATH /opt/oracle/instantclient
RUN apt-get install -y libaio1 libglib2.0-dev \
  && mkdir /opt/oracle \
  && wget https://download.oracle.com/otn_software/linux/instantclient/216000/instantclient-basic-linux.x64-21.6.0.0.0dbru.zip \
  && wget https://download.oracle.com/otn_software/linux/instantclient/216000/instantclient-sdk-linux.x64-21.6.0.0.0dbru.zip \
  && wget https://download.oracle.com/otn_software/linux/instantclient/216000/instantclient-sqlplus-linux.x64-21.6.0.0.0dbru.zip \
  && unzip instantclient-basic-linux.x64-21.6.0.0.0dbru.zip -d /opt/oracle \
  && unzip instantclient-sdk-linux.x64-21.6.0.0.0dbru.zip -d /opt/oracle \
  && unzip instantclient-sqlplus-linux.x64-21.6.0.0.0dbru.zip -d /opt/oracle \
  && mv /opt/oracle/instantclient_21_6 /opt/oracle/instantclient \
  && echo /opt/oracle/instantclient/ | tee -a /etc/ld.so.conf.d/oracle.conf \
  && ldconfig /etc/ld.so.conf.d/oracle.conf \
  && docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient,21.1 \
  && echo 'instantclient,/opt/oracle/instantclient' | pecl install oci8 \
  && docker-php-ext-install pdo_oci \
  && docker-php-ext-enable oci8



###########################################################################
#--------------------------------------------------------------------------
# Final Touch
#--------------------------------------------------------------------------

COPY docker/php-fpm/php8.ini /usr/local/etc/php/php.ini
COPY docker/php-fpm/laravel.ini /usr/local/etc/php/conf.d
COPY docker/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php-fpm/xlaravel.pool.conf /usr/local/etc/php-fpm.d/xlaravel.pool.conf
COPY docker/php-fpm/policy.xml /etc/ImageMagick-6/policy.xml
COPY docker/php-fpm/start.sh /usr/local/sbin/start

USER root

# Clean up
RUN rm -rf /var/www/html \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && rm /var/log/lastlog /var/log/faillog

# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data \
    && usermod -o -u ${PUID} -g www-data www-data \
    && chmod u+x /usr/local/sbin/start

CMD ["/usr/local/sbin/start"]

WORKDIR /var/www

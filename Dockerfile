FROM debian:jessie
MAINTAINER Marcin Wójcik <mwojcik@future-processing.com>

ENV DEBIAN_FRONTEND noninteractive

# Install base packages
RUN apt-get update && \
    apt-get -yq --no-install-recommends  install \
        curl \
        apache2 \
        libapache2-mod-php5 \
        php5-mongo \
        php5-curl \
        ca-certificates \
     && apt-get purge -y --auto-remove \
     && rm -rf /var/lib/apt/lists/*

# Setup Apache configuration
RUN echo "ServerName localhost" | tee /etc/apache2/conf-available/fqdn.conf
COPY run/docker/larmo-server.conf /etc/apache2/sites-available/larmo-server.conf
RUN a2enconf fqdn && a2enmod rewrite && a2dissite 000-default.conf && a2ensite larmo-server

# Install composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

ADD . /data/larmo-server

WORKDIR /data/larmo-server/

RUN composer install

EXPOSE 80:5100

CMD ["apache2ctl", "-DFOREGROUND"]
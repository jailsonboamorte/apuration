FROM phpdockerio/php72-fpm:latest
WORKDIR /var/www/application

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install  php7.2-mysql php7.2-gd \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# install mongo php driver
RUN apt-get update && apt-get install -y php-pear php7.2-dev libcurl4-openssl-dev pkg-config libssl-dev \
&& pecl install mongodb \
&& apt-get install php-mongodb


# Install git
RUN apt-get update \
    && apt-get -y install git \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
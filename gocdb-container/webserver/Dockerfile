# Dockerfile -- Webserver for GOCDB based on PHP5-Apache
#
# Author:   Daniel Mueller/University of Muenster (daniel.mueller@uni-muenster.de)
# Date:     2020-05-04
# Version:  1.0
#
# Note:     This file is considered experimental and should be used with care.
#           It is neither meant to be efficient nor secure.

FROM    php:5-apache
LABEL   maintainer "daniel.mueller@uni-muenster.de"
LABEL   description "Webserver container for GOCDB"

# The GOCDB version number to use
ENV     GOCDB_VERSION="5.7.5"

# Needed for Doctrine
RUN     mkdir -p /usr/local/doctrine
COPY    config/composer.json /usr/local/doctrine
ENV     PATH="/usr/local/doctrine/vendor/bin:${PATH}"

# Install additional dependencies, the GOCDB source files, etc.
RUN     apt-get update && \
        apt-get install -y unzip && \
        a2enmod rewrite && \
        docker-php-ext-install pdo_mysql mbstring && \
        pecl install timezonedb && \
        docker-php-ext-enable timezonedb && \
        cd /usr/local/doctrine && \
        curl -sS https://getcomposer.org/installer -o composer-setup.php && \
        php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
        composer install && \
        cd /tmp && \
        curl -sSL https://github.com/GOCDB/gocdb/archive/${GOCDB_VERSION}.tar.gz -o gocdb.tar.gz && \
        tar -xf gocdb.tar.gz && \
        mv -f gocdb-${GOCDB_VERSION} /var/www/html/gocdb && \
        rm -f gocdb.tar.gz

# Add GOCDB as a virtual host
COPY    config/000-default.conf /etc/apache2/sites-available
# Override the default PHP settings
COPY    config/php.ini $PHP_INI_DIR/

# Last but not least, copy the modified website contents
COPY    html/ /var/www/html

FROM eboraas/apache
MAINTAINER stikks <styccs@gmail.com>

# install dependencies
RUN echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu trusty main" >> /etc/apt/sources.list && \
    apt-key adv --keyserver keyserver.ubuntu.com --recv-key E5267A6C && \
    apt-get update && apt-get install -yqq apt-utils curl nano git 

RUN apt-get install -yqq php5.6 php5.6-gd php5.6-ldap zip unzip php5.6-zip \
    php5.6-sqlite php5.6-pgsql php5.6-mysql php5.6-common \
    php5.6-mcrypt php5.6-xmlrpc php5.6-dom php5.6-xmlwriter php5.6-bcmath php5.6-mbstring && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# add configuration
COPY conf/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
ADD . /opt/IVR

RUN cd /opt/IVR && composer install && /usr/bin/php bootstrap/setup.php && a2enmod rewrite && service apache2 restart

EXPOSE 80

FROM jmar71n/freepbx
MAINTAINER stikks <styccs@gmail.com>
ENV APACHEUSER www-data
ENV ASTERISKUSER asterisk

COPY conf/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
ADD . /opt/IVR
ADD conf/freepbx/proxy /opt/proxy

COPY conf/freepbx/extensions_custom.conf /etc/asterisk/extensions_custom.conf
COPY conf/freepbx/sip_custom.conf /etc/asterisk/sip_custom.conf
COPY conf/freepbx/ports.conf /etc/apache2/ports.conf
COPY conf/freepbx/cdr.conf /etc/asterisk/cdr.conf

COPY conf/freepbx/hmm.php conf/freepbx/entry.php conf/freepbx/dependencies.php \
    conf/freepbx/confirm.php conf/freepbx/impression.php conf/freepbx/sub.php /var/lib/asterisk/agi-bin/

# install dependencies
RUN apt-get remove -yqq php*

RUN echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu trusty main" >> /etc/apt/sources.list && \
    apt-key adv --keyserver keyserver.ubuntu.com --recv-key E5267A6C && \
    add-apt-repository -y ppa:mc3man/trusty-media && \
    apt-get update && apt-get install --force-yes -yqq ffmpeg gstreamer0.10-ffmpeg

RUN apt-get install --force-yes -yqq apt-utils curl nano git ssh zip unzip telnet && \
    apt-get install --force-yes -yqq php5.6 php5.6-mbstring php5.6-pgsql php5.6-mysql \
    php5.6-xmlwriter php5.6-bcmath php5.6-curl php5.6-zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir /var/lib/asterisk/sounds/defaults && \
    chown -R $ASTERISKUSER. /var/lib/asterisk/sounds/defaults && \
    chmod -R 777 /var/lib/asterisk/sounds/defaults && \
    mkdir /var/lib/asterisk/sounds/inactive && \
    mkdir /var/lib/asterisk/sounds/inactive/etisalat && \
    chown -R $ASTERISKUSER. /var/lib/asterisk/sounds/inactive && \
    chmod -R 777 /var/lib/asterisk/sounds/inactive && \
    mkdir /var/lib/asterisk/sounds/files && \
    mkdir /var/lib/asterisk/sounds/files/etisalat && \
    mkdir /var/lib/asterisk/sounds/files/inactive && \
    mkdir /var/lib/asterisk/sounds/files/inactive/etisalat && \
    chown -R $ASTERISKUSER. /var/lib/asterisk/sounds/files && \
    chmod -R 777 /var/lib/asterisk/sounds/files && \
    chown -R $ASTERISKUSER. /var/lib/asterisk/sounds/inactive && \
    chmod -R 777 /var/lib/asterisk/sounds/inactive && \
    chown -R $ASTERISKUSER. /var/lib/asterisk/agi-bin && \
    cd /opt/IVR && \
    mkdir /opt/IVR/files && \
    mkdir /opt/IVR/files/etisalat && \
    mkdir /opt/IVR/files/inactive && \
    mkdir /opt/IVR/files/deleted && \
    mkdir /opt/IVR/files/inactive/etisalat && \
    mkdir /opt/IVR/logs/ && \
    mkdir /opt/backup && \
    chmod 777 -R /opt/IVR/logs && \
    chmod 777 -R /opt/IVR/files && \
    chown -R $ASTERISKUSER. files && \
    composer install

COPY conf/freepbx/wrong.wav /var/lib/asterisk/sounds/defaults/backup.wav

RUN service mysql restart && \
    fwconsole restart && \
    a2enmod rewrite && \
    export TERM=xterm && \
    service apache2 restart

# setup background task cron
#RUN crontab -e && \
#    crontab -l > test && \
#    echo "00 00 * * * /usr/bin/php /opt/IVR/background.php" >> test && \
#    crontab test && \
#    rm test
#
## setup cron
#RUN crontab -e && \
#    crontab -l > backup && \
#    echo "30 00 * * * /usr/bin/php /opt/IVR/backup.php" >> backup && \
#    crontab test && \
#    rm test

#RUN /usr/bin/php bootstrap/setup.php

EXPOSE 80


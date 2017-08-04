FROM jmar71n/freepbx
MAINTAINER stikks <styccs@gmail.com>
ENV ASTERISKUSER asterisk

RUN apt-get update && apt-get install -yqq apt-utils curl nano git  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY extensions_custom.conf /etc/asterisk/extensions_custom.conf
COPY hmm.php entry.php dependencies.php confirm.php impression.php sub.php /var/lib/asterisk/agi-bin/

ADD . /opt/IVR
RUN cd /opt/IVR && composer install && chown -R $ASTERISKUSER. /var/lib/asterisk/agi-bin && mkdir /var/lib/asterisk/sounds/defaults && mkdir /var/lib/asterisk/sounds/files && chown -R $ASTERISKUSER. /var/lib/asterisk/sounds/files && /var/lib/asterisk/sounds/files/etisalat && /var/lib/asterisk/sounds/files/tm30 && /var/lib/asterisk/sounds/files/inactive && chmod -R 777 /var/lib/asterisk/sounds/files && chown -R $ASTERISKUSER. /var/lib/asterisk/sounds/defaults && chmod -R 777 /var/lib/asterisk/sounds/defaults && service mysql restart && fwconsole restart
RUN usermod --password fileopen root && service ssh start

EXPOSE 80

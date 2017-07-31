FROM jmar71n/freepbx
MAINTAINER stikks <styccs@gmail.com>
ENV ASTERISKUSER asterisk

RUN apt-get update && apt-get install -yqq apt-utils curl nano git  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY extensions_custom.conf /etc/asterisk/extensions_custom.conf
COPY hmm.php entry.php dependencies.php confirm.php impression.php sub.php /var/lib/asterisk/agi-bin/

ADD . /opt/IVR
RUN cd /opt/IVR && composer install && chown -R $ASTERISKUSER. /var/lib/asterisk/agi-bin \
 && mkdir /var/lib/asterisk/sounds/defaults  && mkdir /var/lib/asterisk/sounds/files \
 && chown -R $ASTERISKUSER. /var/lib/asterisk/sounds/files && chmod -R 777 /var/lib/asterisk/sounds/files \
 && chown -R $ASTERISKUSER. /var/lib/asterisk/sounds/defaults && chmod -R 777 /var/lib/asterisk/sounds/defaults
 && service mysql restart && fwconsole restart && service ssh restart \
 && adduser tm30 && passwd tm30

EXPOSE 80

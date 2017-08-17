FROM jmar71n/freepbx
MAINTAINER stikks <styccs@gmail.com>
ENV ASTERISKUSER asterisk

RUN apt-get update && apt-get install -yqq apt-utils curl nano git && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY extensions_custom.conf /etc/asterisk/extensions_custom.conf
COPY sip_custom.conf /etc/asterisk/sip_custom.conf
COPY hmm.php entry.php dependencies.php confirm.php impression.php sub.php /var/lib/asterisk/agi-bin/

RUN apt-get update && \
  mkdir -p /var/lib/asterisk/sounds/defaults && \
  chown -R $ASTERISKUSER. /var/lib/asterisk/sounds/defaults && \
  chmod -R 777 /var/lib/asterisk/sounds/defaults && \
  mkdir -p /var/lib/asterisk/sounds/inactive && \
  chown -R $ASTERISKUSER. /var/lib/asterisk/sounds/inactive && \
  chmod -R 777 /var/lib/asterisk/sounds/inactive && \
  mkdir -p /var/lib/asterisk/sounds/files && \
  chown -R $ASTERISKUSER. /var/lib/asterisk/sounds/files && \
  chmod -R 777 /var/lib/asterisk/sounds/files && \
  mkdir -p /var/lib/asterisk/sounds/files/etisalat && \
  mkdir -p /var/lib/asterisk/sounds/files/inactive/etisalat && \
  chown -R $ASTERISKUSER. /var/lib/asterisk/agi-bin

COPY wrong.wav /var/lib/asterisk/defaults/backup.wav

ADD . /opt/IVR
RUN cd /opt/IVR  && \
  composer install && \
  service mysql restart && \
  fwconsole restart && \
  export TERM=xterm
#ENTRYPOINT service ssh restart && bash

EXPOSE 80
EXPOSE 22

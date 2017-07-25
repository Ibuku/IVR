FROM jmar71n/freepbx
MAINTAINER stikks <styccs@gmail.com>
ENV ASTERISKUSER asterisk

RUN apt-get update && apt-get install -yqq apt-utils curl nano git  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY extensions_custom.conf /etc/asterisk/extensions_custom.conf
COPY hmm.php entry.php dependencies.php confirm.php impression.php sub.php /var/lib/asterisk/agi-bin/

ADD . /opt/IVR
RUN cd /opt/IVR && composer install && chown -R $ASTERISKUSER. /var/lib/asterisk/agi-bin && service mysql restart && fwconsole restart

EXPOSE 80

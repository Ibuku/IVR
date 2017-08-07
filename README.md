To setup the application, 

Install docker and docker-compose

docker-compose up -d

docker ps

Setup IVR application

docker exec -it {container ID of stikks/ivr} bash

apt-get install php-pgsql

cd /opt/IVR

php bootstrap/setup.php

mkdir -p files/etisalat
mkdir -p files/tm30
mkdir -p  files/inactive

chown -R www-data. files/
chmod -R 777 files/

Change password of ssh user on freepbx container

docker exec -it {container ID of stikks/freepbx} bash

service ssh start

passwd root

mkdir -p /var/lib/asterisk/sounds/files/etisalat
mkdir -p /var/lib/asterisk/sounds/files/tm30
mkdir -p  /var/lib/asterisk/sounds/files/inactive

chown -R asterisk. /var/lib/asterisk/sounds/files/
chmod -R 777 /var/lib/asterisk/sounds/files/

docker volume create --name=freepbx-data

docker volume create --name=asterisk-conf
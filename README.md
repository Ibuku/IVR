To setup the application, 

Install docker and docker-compose

docker-compose up -d

docker ps

Setup IVR application

docker exec -it {container ID of stikks/ivr} bash

apt-get install php-pgsql

cd /opt/IVR

php bootstrap/setup.php

mkdir files/etisalat
mkdir files/tm30
mkdir files/inactive

chown -R www-data. files/
chmod -R 777 files/

Change password of ssh user on freepbx container

docker exec -it {container ID of stikks/freepbx} bash

service ssh start

passwd root
To setup the application, 

Install docker and docker-compose

docker-compose up -d

docker ps

Setup IVR application

docker exec -it {container ID of stikks/ivr-app} bash

cd /opt/IVR

nano /etc/apache2/ports.conf

add Listen 8900

nano /etc/apache2/sites-available/000-default.conf

add content of conf/apache/000-default.conf

php bootstrap/setup.php

docker volume create --name=freepbx-data
    
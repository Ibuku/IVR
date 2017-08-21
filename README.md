To setup the application, 

Install docker and docker-compose

docker volume create --name=freepbx-data  
docker volume create --name=files-data
docker volume create --name=postgres-data
docker volume create --name=elasticsearch-data
docker volume create --name=agi-defaults
docker volume create --name=agi-sounds
docker volume create --name=redis-data

docker-compose up -d

docker ps

Setup IVR application

docker exec -it {container ID of stikks/ivr-app} bash

php bootstrap/setup.php

visit http://localhost:80 on your browser,
> navigate to Admin -> Asterisk Modules
    > Manually loaded Modules > Add cdr_csv.so (input field to the right)

asterisk -rvvv
   > module load cdr_csv.so
    > module unload cdr_adaptive_odbc.so
    > dialplan reload
    > sip reload
    
  

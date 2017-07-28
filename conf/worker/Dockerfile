FROM node
MAINTAINER <styccs@gmail.com>

ADD . /opt/app
RUN cd /opt/app && npm install && npm install pm2@latest -g && pm2 start app/app.js

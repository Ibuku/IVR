FROM node
MAINTAINER <styccs@gmail.com>

ADD . /opt/app
RUN cd /opt/app && npm install
RUN node app/app.js

EXPOSE 4043

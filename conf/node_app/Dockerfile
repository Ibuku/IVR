#FROM keymetrics/pm2-docker-alpine:latest
#ADD app /opt/app
#COPY pm2.json /app
#RUN cd /opt/app && npm install -g && \
#    apk add --update tzdata git nano && \
#    echo "Africa/Lagos" > /etc/timezone && \
#    cp /usr/share/zoneinfo/Africa/Lagos /etc/localtime

#CMD [ "pm2-docker", "start", "pm2.json" ]
FROM node:alpine
LABEL maintainer="Stikks <styccs@gmail.com>"

RUN npm install pm2 -g

VOLUME ["/app"]

WORKDIR /app

ADD app /opt/app
COPY pm2.json /app

RUN apk add --update tzdata git nano && \
    echo "Africa/Lagos" > /etc/timezone && \
    cp /usr/share/zoneinfo/Africa/Lagos /etc/localtime

RUN cd /opt/app && npm install

# Expose ports
EXPOSE 43554 4043

# Start process.yml
CMD ["pm2-docker", "start", "pm2.json"]
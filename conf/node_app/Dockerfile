FROM keymetrics/pm2-docker-alpine:latest
ADD app /opt/app
COPY pm2.json /app
RUN cd /opt/app && npm install -g && \
    apk add --update go git nano && \
    echo "Africa/Lagos" > /etc/timezone && \
    cp /usr/share/zoneinfo/Africa/Lagos /etc/localtime

CMD [ "pm2-docker", "start", "pm2.json" ]
#FROM node:alpine
#LABEL maintainer="Stikks <styccs@gmail.com>"
#
#RUN npm install pm2 -g
#
#VOLUME ["/app"]
#
#WORKDIR /app
#
#ADD app /opt/app
#COPY pm2.json /app
#RUN cd /opt/app && npm install -g
##    apk update && \
##    apk add --update git nano curl
#
## Start process.yml
#CMD ["pm2-docker", "start", "--auto-exit", "--env", "production", "pm2.json"]
#
# Expose ports
EXPOSE 43554 4043
FROM keymetrics/pm2-docker-alpine:latest
ADD app /opt/app
COPY pm2.json /app
RUN cd /opt/app && npm install -g
CMD [ "pm2-docker", "start", "pm2.json" ]

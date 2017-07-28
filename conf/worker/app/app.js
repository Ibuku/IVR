#!/usr/bin/env node
var amqp = require('amqplib/callback_api');
var rq = require('request-promise');

amqp.connect('amqp://rabbit', function(err, conn) {
    console.log(err);
    console.log(conn);
    if (!err) {
        conn.createChannel(function(err, ch) {
            var q = 'ivr';

            ch.assertQueue(q, {durable: false});
            ch.prefetch(1);
            console.log(" [*] Waiting for messages in %s. To exit press CTRL+C", q);
            ch.consume(q, function(msg) {
                var _data = msg.content.toString();
                var data = JSON.parse(_data);
                var options = {
                    method: 'POST',
                    uri: data.url,
                    body: data,
                    json: true
                };
                rq(options).then(function(resp) {
                    console.log(resp);
                })
                    .catch(function(err) {
                        console.log(err);
                    });
                // request({
                //     url: data.url, //URL to hit
                //     method: 'POST',
                //     data: data
                // }, function (error, response, body) {
                //     console.log(body);
                //     console.log(error);
                // });
            }, {noAck: false});
        });
    }
});

#!/usr/bin/env node
var amqp = require('amqplib/callback_api');
var request = require('request-promise');

amqp.connect('amqp://localhost', function(err, conn) {
    conn.createChannel(function(err, ch) {
        var q = 'ivr';

        ch.assertQueue(q, {durable: false});
        console.log(" [*] Waiting for messages in %s. To exit press CTRL+C", q);
        ch.consume(q, function(msg) {
            var body = JSON.parse(msg.content.toString());
            request({
                url: body.url, //URL to hit
                method: 'POST',
                data: body
            }, function (error, response, body) {
                console.log(body);
                console.log(error);
            });
        }, {noAck: true});
    });
});
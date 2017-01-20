#!/usr/bin/env node
var amqp = require('amqplib/callback_api');
var request = require('request-promise');

amqp.connect('amqp://localhost', function(err, conn) {
    conn.createChannel(function(err, ch) {
        var q = 'ivr';

        ch.assertQueue(q, {durable: false});
        console.log(" [*] Waiting for messages in %s. To exit press CTRL+C", q);
        ch.consume(q, function(msg) {
            var _data = msg.content.toString();
            request({
                url: _data.body.url, //URL to hit
                method: 'POST',
                data: _data.body
            }, function (error, response, body) {
                console.log(body);
                console.log(error);
            });
        }, {noAck: true});
    });
});
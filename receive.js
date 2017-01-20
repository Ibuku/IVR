#!/usr/bin/env node

var amqp = require('amqplib/callback_api');

amqp.connect('amqp://localhost', function(err, conn) {
    conn.createChannel(function(err, ch) {
        var q = 'ivr';

        ch.assertQueue(q, {durable: false});
        console.log(" [*] Waiting for messages in %s. To exit press CTRL+C", q);
        ch.consume(q, function(msg) {
            var body = JSON.parse(msg.content.toString());
            $.post(body.url, body, function (data, status) {
                console.log(data);
                console.log(status);
            })
        }, {noAck: true});
    });
});
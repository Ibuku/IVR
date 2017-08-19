#!/usr/bin/env node

var amqp = require('amqplib/callback_api');
var rq = require('request-promise');

function bail(err, conn) {
    console.error(err);
    if (conn) conn.close(function() { process.exit(1); });
}

function on_connect(err, conn) {
    if (err !== null) return bail(err);
    process.once('SIGINT', function() { conn.close(); });

    var q = 'ivr';

    conn.createChannel(function(err, ch) {
        if (err !== null) return bail(err, conn);
        ch.assertQueue(q, {durable: true}, function(err, _ok) {
            ch.consume(q, doWork, {noAck: false});
            console.log(" [*] Waiting for messages. To exit press CTRL+C");
        });

        function doWork(msg) {
            var _data = msg.content.toString();
            var data = JSON.parse(_data);
            console.log(" [x] Received '%s'", data);
            var options = {
                method: 'POST',
                uri: data.url,
                body: data,
                json: true
            };
            rq(options).then(function(resp) {
                console.log(resp);
            }).catch(function(err) {
                console.log(err);
            });
        }
    });
}

amqp.connect('amqp://rabbit', on_connect);
#!/usr/bin/env node
var amqp = require('amqplib/callback_api');
var request = require('request-promise');

amqp.connect('amqp://localhost', function(err, conn) {
    conn.createChannel(function(err, ch) {
        var q = 'ivr';

        ch.assertQueue(q, {durable: false});
        ch.prefetch(1);
        console.log(" [*] Waiting for messages in %s. To exit press CTRL+C", q);
        ch.consume(q, function(msg) {
            var _data = msg.content.toString();
            var data = JSON.parse(_data);
            request({
                url: data.url, //URL to hit
                method: 'POST',
                data: data
            }, function (error, response, body) {
                console.log(body);
                console.log(error);
            });
        }, {noAck: false});
    });
});


[new]
exten => 39604,1,Verbose(1, Extension 39604)
exten => 39604,2,Progress()
exten => 39604,3,Answer()
exten => 39604,4,AGI(entry.php)
exten => 39604,5,Playback(incorrect)

exten => 39604,6,Playback(${play_path})
exten => 39604,7,AGI(module.php)

exten => 39604,n,Background(defaults/subscribe)
exten => 39604,n,GotoIf(condition?label1:label2)

exten => 39603,1,Background(defaults/confirmation)
exten => 39603,n,GotoIf(condition?label1:label2)

exten => 39604,n,AGI(subscribe.php)
exten => 39604,n,Playback(${response_path})
exten => 39604,n,Hangup()

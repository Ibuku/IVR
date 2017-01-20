var amqp = require('amqplib/callback_api');
var express = require('express');
var router = express.Router();

router.post('/elasticsearch/:type/create', function (req, res, next) {

    var body = req.body;

    try{
        body = JSON.parse(body);
    }
    catch (err){}

    amqp.connect('amqp://localhost', function(err, conn) {
        body.url = 'http://localhost:4045/ivr/elasticsearch/' + req.params.type + '/create';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: false});
            ch.sendToQueue(q, new Buffer(JSON.stringify({body: body})));
            console.log(" [x] Sent Message");
        });
    });
});

router.post('/elasticsearch/cdr/missing', function (req, res, next) {

    var body = req.body;

    try{
        body = JSON.parse(body);
    }
    catch (err){}

    amqp.connect('amqp://localhost', function(err, conn) {
        body.url = 'http://localhost:4045/ivr/elasticsearch/cdr/missing';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: false});
            ch.sendToQueue(q, new Buffer(JSON.stringify({body: body})));
            console.log(" [x] Sent Message");
        });
    });
});

router.post('/cdr/impression', function (req, res, next) {

    var body = req.body;

    try{
         body = JSON.parse(body);
    }
    catch (err){}


    amqp.connect('amqp://localhost', function(err, conn) {
        body.url = 'http://localhost:4045/ivr/cdr/impression';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: false});
            ch.sendToQueue(q, new Buffer(JSON.stringify({body: body})));
            console.log(" [x] Sent Message");
        });
    });

});

router.post('/cdr/subscribe', function (req, res, next) {

    var body = req.body;

    try{
        body = JSON.parse(body);
    }
    catch (err){}

    amqp.connect('amqp://localhost', function(err, conn) {
        body.url = 'http://localhost:4045/ivr/cdr/subscribe';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: false});
            ch.sendToQueue(q, new Buffer(JSON.stringify({body: body})));
            console.log(" [x] Sent Message");
        });
    });
});

router.post('/cdr/confirmation', function (req, res, next) {

    var body = req.body;

    try{
        body = JSON.parse(body);
    }
    catch (err){}


    amqp.connect('amqp://localhost', function(err, conn) {
        body.url = 'http://localhost:4045/ivr/cdr/confirmation';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: false});
            ch.sendToQueue(q, new Buffer(JSON.stringify({body: body})));
            console.log(" [x] Sent Message");
        });
    });
});

router.post('/cdr/insufficient', function (req, res, next) {

    var body = req.body;

    try{
        body = JSON.parse(body);
    }
    catch (err){}

    amqp.connect('amqp://localhost', function(err, conn) {
        body.url = 'http://localhost:4045/ivr/cdr/insufficient';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: false});
            ch.sendToQueue(q, new Buffer(JSON.stringify({body: body})));
            console.log(" [x] Sent Message");
        });
    });
});

router.post('/cdr/already_sub', function (req, res, next) {

    var body = req.body;

    try{
        body = JSON.parse(body);
    }
    catch (err){}

    amqp.connect('amqp://localhost', function(err, conn) {
        body.url = 'http://localhost:4045/ivr/cdr/already_sub';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: false});
            ch.sendToQueue(q, new Buffer(JSON.stringify({body: body})));
            console.log(" [x] Sent Message");
        });
    });
});

router.post('/cdr/success', function (req, res, next) {

    var body = req.body;

    try{
        body = JSON.parse(body);
    }
    catch (err){}

    amqp.connect('amqp://localhost', function(err, conn) {
        body.url = 'http://localhost:4045/ivr/cdr/success';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: false});
            ch.sendToQueue(q, new Buffer(JSON.stringify({body: body})));
            console.log(" [x] Sent Message");
        });
    });
});

router.post('/cdr/failed', function (req, res, next) {

    var body = req.body;

    try{
        body = JSON.parse(body);
    }
    catch (err){}

    amqp.connect('amqp://localhost', function(err, conn) {
        body.url = 'http://localhost:4045/ivr/cdr/failed';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: false});
            ch.sendToQueue(q, new Buffer(JSON.stringify({body: body})));
            console.log(" [x] Sent Message");
        });
    });
});

router.get('/', function (req, res, next) {
    res.send('respond with a resource');
});

module.exports = router;
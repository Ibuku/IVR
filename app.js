var ctx = require('rabbit.js').createContext('amqp://localhost');

ctx.on('ready', function() {
    var pub = ctx.socket('PUB');
    pub.connect('events', function() {
        pub.write(JSON.stringify(req.body), 'utf8');
    });
});

ctx.on('ready', function() {
    var sub = ctx.socket('SUB');
    sub.pipe(process.stdout);
    sub.connect('events', function(data) {
        console.log('data: ' + data);
    });
});

ctx.on('error', console.warn);

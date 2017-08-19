function getStart(d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day + (day == 0 ? -6:0);
    var x = new Date(d.setDate(diff));
    x.setUTCHours(0,0,0,0);
    return new Date(x);
}

var groupBy = function (xs, key) {
    return xs.reduce(function (rv, x) {
            (rv[x[key]] = rv[x[key]] || []).push(x);
            return rv;
        }, {}
    );
};

var amqp = require('amqplib/callback_api');
var express = require('express');
var router = express.Router();

// var redis = require("redis");
// redis_client = redis.createClient();
//
// redis_client.on("error", function (err) {
//     console.log("Error " + err);
// });

var elasticsearch = require('elasticsearch');

//connect to elastic search
var client = elasticsearch.Client({
    host: 'elasticsearch:9200',
    log: 'trace'
});

client.ping({
    // ping usually has a 3000ms timeout
    requestTimeout: Infinity,
    // undocumented params are appended to the query string
    hello: "elasticsearch!"
}, function (error) {
    if (error) {
        console.log(error);
        console.trace('elasticsearch cluster is down!');
    } else {
        console.log('All is well');
    }
});

//Check if index exist or create it
client.exists({
    index: 'ivr'
}, function (error, exists) {
    if (exists == true) {
        console.log("This exists")
    } else {
        client.indices.create({
            index: 'ivr',
            body: {
                "mappings": {
                    "campaign": {
                        "properties": {
                            "play_path": {
                                "type": "string",
                                "index": "not_analyzed"
                            },
                            "name": {
                                "type": "string",
                                "index": "not_analyzed"
                            }
                        }
                    }
                }
            }
        }, function (err, resp, status) {
            if (err) {
                console.log("This works");
            }
            else {
                console.log("create", resp);
            }
        });
    }
});

router.post('/elasticsearch/:type/create', function (req, res, next) {

    amqp.connect('amqp://rabbit', function(err, conn) {
        req.body.url = 'http://app:4043/ivr/elasticsearch/' + req.params.type + '/create';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: true});
            ch.sendToQueue(q, new Buffer(JSON.stringify(req.body)));
            console.log(" [x] Sent Message");
            res.sendStatus(200);
        });
    });
});

router.post('/elasticsearch/cdr/missing', function (req, res, next) {

    amqp.connect('amqp://rabbit', function(err, conn) {
        req.body.url = 'http://app:4043/ivr/elasticsearch/cdr/missing';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: true});
            ch.sendToQueue(q, new Buffer(JSON.stringify(req.body)));
            console.log(" [x] Sent Message");
            res.sendStatus(200);
        });
    });
});

router.post('/cdr/impression', function (req, res, next) {

    amqp.connect('amqp://rabbit', function(err, conn) {
        req.body.url = 'http://app:4043/ivr/cdr/impression';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: true});
            ch.sendToQueue(q, new Buffer(JSON.stringify(req.body)));
            console.log(" [x] Sent Message");
            res.sendStatus(200);
        });
    });

});

router.post('/cdr/subscribe', function (req, res, next) {

    amqp.connect('amqp://rabbit', function(err, conn) {
        req.body.url = 'http://app:4043/ivr/cdr/subscribe';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: true});
            ch.sendToQueue(q, new Buffer(JSON.stringify(req.body)));
            console.log(" [x] Sent Message");
            res.sendStatus(200);
        });
    });
});

router.post('/cdr/confirmation', function (req, res, next) {

    amqp.connect('amqp://rabbit', function(err, conn) {
        req.body.url = 'http://app:4043/ivr/cdr/confirmation';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: true});
            ch.sendToQueue(q, new Buffer(JSON.stringify(req.body)));
            console.log(" [x] Sent Message");
            res.sendStatus(200);
        });
    });
});

router.post('/cdr/insufficient', function (req, res, next) {

    amqp.connect('amqp://rabbit', function(err, conn) {
        req.body.url = 'http://app:4043/ivr/cdr/insufficient';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: true});
            ch.sendToQueue(q, new Buffer(JSON.stringify(req.body)));
            console.log(" [x] Sent Message");
            res.sendStatus(200);
        });
    });
});

router.post('/cdr/already_sub', function (req, res, next) {

    amqp.connect('amqp://rabbit', function(err, conn) {
        req.body.url = 'http://app:4043/ivr/cdr/already_sub';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: true});
            ch.sendToQueue(q, new Buffer(JSON.stringify(req.body)));
            console.log(" [x] Sent Message");
            res.sendStatus(200);
        });
    });
});

router.post('/cdr/success', function (req, res, next) {

    amqp.connect('amqp://rabbit', function(err, conn) {
        req.body.url = 'http://app:4043/ivr/cdr/success';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: true});
            ch.sendToQueue(q, new Buffer(JSON.stringify(req.body)));
            console.log(" [x] Sent Message");
            res.sendStatus(200);
        });
    });
});

router.post('/cdr/failed', function (req, res, next) {

    amqp.connect('amqp://rabbit', function(err, conn) {
        req.body.url = 'http://app:4043/ivr/cdr/failed';
        conn.createChannel(function(err, ch) {
            var q = 'ivr';
            ch.assertQueue(q, {durable: true});
            ch.sendToQueue(q, new Buffer(JSON.stringify(req.body)));
            console.log(" [x] Sent Message");
            res.sendStatus(200);
        });
    });
});

/*Number of campaign over a certain period*/
router.get('/no_of_campaign', function (req, res, next) {

    var sevenDays = new Date(new Date().getTime() - (6 * 24 * 60 * 60 * 1000));
    sevenDays.setUTCHours(0, 0, 0, 0);
    var today = new Date();
    today.setUTCHours(23, 59, 59, 59);
    client.search({
        index: "ivr",
        type: "statuses",
        body: {
            "query": {
                "bool": {
                    "must": [
                        {
                            "match_all": {}
                        }
                    ],
                    "filter": [
                        {
                            "range": {
                                "created_at": {
                                    "gte": sevenDays,
                                    "lte": today
                                }
                            }
                        }
                    ]
                }
            }
        }
    }).then(function (resp) {
        var result = resp.hits.hits;

        var data = result.map(function (_obj) {
            return _obj._source
        });

        var ar = groupBy(data, "campaign_name");

        res.setHeader('Content-Type', 'application/json');
        res.send(JSON.stringify({result: ar}));
    });
});

router.get('/campaign/:id/data', function (req, res, next) {

    var campaign_id = req.params.id;
    var sevenDays = new Date(new Date().getTime() - (6 * 24 * 60 * 60 * 1000));
    sevenDays.setUTCHours(0, 0, 0, 0);
    var today = new Date(new Date().getTime() + (24 * 60 * 60 * 1000));
    today.setUTCHours(23, 59, 59, 999);
    client.search({
        index: "ivr",
        type: "statuses",
        body: {
            "query": {
                "bool": {
                    "must": [
                        {
                            "match_all": {}
                        }
                    ],
                    "filter": [
                        {
                            "range": {
                                "created_at": {
                                    "gte": sevenDays,
                                    "lte": today
                                }
                            }
                        }
                    ]
                }
            }
        }
    }).then(function (resp) {
        var result = resp.hits.hits;
        var data = result.filter(function (_obj) {
            if (_obj._source.campaign_id == campaign_id) {
                return _obj._source
            }
        });

        var ar = data.map(function (_obj) {
            return _obj._source
        });

        res.setHeader('Content-Type', 'application/json');
        res.send(JSON.stringify({result: ar}));
    });
});

//Campign impressions
router.get('/impressions/:campaign_id', function (req, res, next) {
    var sevenDays = new Date(new Date().getTime() - (6 * 24 * 60 * 60 * 1000));
    sevenDays.setHours(0, 0, 0, 0);
    var today = new Date();
    today.setHours(0, 0, 0, 0);
    sevenDays.toDateString
    today.toDateString

    client.search({
        index: "ivr",
        type: "cdr",
        body: {
            "query": {
                "constant_score": {
                    "filter": {
                        "bool": {
                            "must": [
                                {
                                    "term": {
                                        "userfield": body.params.campaign_id
                                    }
                                }
                            ],
                            "should": [
                                {
                                    "range": {
                                        "start": {
                                            "from": sevenDays,
                                            "to": today
                                        }
                                    }
                                }
                            ]
                        }
                    }
                }
            }
        }
    }).then(function (resp) {
        var result = resp.hits.hits;

        var ar = groupBy(result, "created_at");

        var cat = Object.keys(ar);
    })

});

router.post('/campaign/:id/download', function (req, res, next) {

    var campaign_id = req.params.id;
    var start = new Date(req.body.start_date);
    var end = new Date(req.body.end_date);
    start.toDateString;
    end.toDateString;
    //Add javacript check date
    client.search({
        index: "ivr",
        type: "statuses",
        body: {
            "query": {
                "constant_score": {
                    "filter": {
                        "bool": {
                            "should": [
                                {
                                    "range": {
                                        "created_at": {
                                            "from": start,
                                            "to": end
                                        }
                                    }
                                }
                            ]
                        }
                    }
                }
            }
        }
    }).then(function (resp) {
        var result = resp.hits.hits;

        var data = result.filter(function (_obj) {
            if (_obj._source.campaign_id == campaign_id) {
                return _obj._source
            }
        });

        var ar = data.map(function (_obj) {
            return _obj._source
        });

        res.setHeader('Content-Type', 'application/json');
        res.send(JSON.stringify({result: ar}));
    });
});

router.post('/campaign/download', function (req, res, next) {

    var start = new Date(req.body.start_date);
    var end = new Date(req.body.end_date);
    start.toDateString;
    end.toDateString;
    //Add javacript check date
    client.search({
        index: "ivr",
        type: "statuses",
        body: {
            "query": {
                "constant_score": {
                    "filter": {
                        "bool": {
                            "should": [
                                {
                                    "range": {
                                        "created_at": {
                                            "from": start,
                                            "to": end
                                        }
                                    }
                                }
                            ]
                        }
                    }
                }
            }
        }
    }).then(function (resp) {
        var result = resp.hits.hits;

        var data = result.map(function (_obj) {
            return _obj._source
        });

        res.setHeader('Content-Type', 'application/json');
        res.send(JSON.stringify({result: data}));
    });
});

router.post('/elasticsearch/:type/:id/update', function (req, res, next) {

    if (req.params.type == "campaign") {
        var start_date = new Date(req.body.start_date);
        var end_date = new Date(req.body.end_date);
        start_date.toDateString;
        end_date.toDateString;
        client.get({
            index: 'ivr',
            type: 'campaign',
            id: req.body.id
        }, function (err, resp) {
            client.update({
                index: 'ivr',
                type: 'campaign',
                id: req.body.id,
                body: {
                    doc: {
                        name: req.body.name,
                        description: req.body.description,
                        start_date: start_date,
                        end_date: end_date,
                        play_path: req.body.play_path
                    }
                }
            }, function (error, response) {
                res.setHeader('Content-Type', 'application/json');
                res.send(JSON.stringify({response: response, error: error}));
            });
        });
    }
    else if (req.params.type == "action") {
        client.get({
            index: 'ivr',
            type: 'action',
            id: req.body.id
        }, function (err, resp) {
            client.update({
                index: 'ivr',
                type: 'action',
                id: req.body.id,
                body: {
                    doc: {
                        value: req.body.value,
                        number: req.body.number,
                        body: req.body.body,
                        request: req.body.request,
                        parameter: req.body.parameter,
                        repeat_param: req.body.repeat_param,
                        confirm: req.body.confirm
                    }
                }
            }, function (error, response) {
                res.setHeader('Content-Type', 'application/json');
                res.send(JSON.stringify({response: response, error: error}));
            });
        });
    }
});

router.get('/elasticsearch/:type/all', function (req, res, next) {
    //All index object for a type
    res.setHeader('Content-Type', 'application/json');
    client.search({
        index: 'ivr',
        type: req.params.type,
        body: {
            "query": {
                "constant_score": {
                    "filter": {
                        "match_all": {}
                    }
                }
            }
        }
    }).then(function (resp) {
        result = resp.hits.hits;
        var data = result.map(function (_obj) {
            return _obj._source
        });
        res.send(JSON.stringify({message: data}));
    });
});

router.post('/elasticsearch/cdr/missing', function (req, res, next) {

    client.index({
        index: 'ivr',
        id: req.body.uniqueid,
        type: 'cdr',
        body: {
            "src": req.body.src,
            "clid": req.body.clid,
            "duration": req.body.duration,
            "userfield": req.body.name,
            "uniqueid": req.body.uniqueid,
            "impression": false,
            "billsec": req.body.billsec,
            "is_successful": false,
            "created_at": created
        }
    }, function (err, resp, status) {
        res.setHeader('Content-Type', 'application/json');
        res.send(JSON.stringify({response: true, error: err}));
    });
});

router.get('/data/today', function (req, res, next) {

    var day = new Date();
    day.setUTCHours(0, 0, 0, 0);
    var right_now = new Date();
    right_now.setUTCHours(23, 59, 59, 999);
    var total_data = {"data": []};

    // today CDR records
    client.search({
        index: 'ivr',
        type: 'statuses',
        body: {
            "query": {
                "bool": {
                    "must": [
                        {
                            "match_all": {}
                        }
                    ],
                    "filter": [
                        {
                            "range": {
                                "created_at": {
                                    "gte": day,
                                    "lte": right_now
                                }
                            }
                        }
                    ]
                }
            }
        }
    }).then(function (resp) {
        var result = resp.hits.hits;
        if (result.length > 0) {
            total_data.data = result.map(function (_obj) {
                return _obj._source
            });
        }
        res.send(JSON.stringify(total_data));
    });
});

router.get('/data/yesterday', function (req, res, next) {

    // yesterday cdr records
    var yesterday_start = new Date(new Date().getTime() - (24 * 60 * 60 * 1000));
    yesterday_start.setUTCHours(0, 0, 0, 0);
    var yesterday_end = new Date(new Date().getTime() - (24 * 60 * 60 * 1000));
    yesterday_end.setUTCHours(23,59,59,999);

    var total_data = {"data": []};

    // yesterday CDR records
    client.search({
        index: 'ivr',
        type: 'statuses',
        body: {
            "query": {
                "bool": {
                    "must": [
                        {
                            "match_all": {}
                        }
                    ],
                    "filter": [
                        {
                            "range": {
                                "created_at": {
                                    "gte": yesterday_start,
                                    "lte": yesterday_end
                                }
                            }
                        }
                    ]
                }
            }
        }
    }).then(function (resp) {
        var yer_result = resp.hits.hits;
        if (yer_result.length > 0) {
            total_data.yesterday = yer_result.map(function (__obj) {
                return __obj._source
            });
        }
        res.send(JSON.stringify(total_data));
    });
});

router.get('/data/week', function (req, res, next) {

    var week_start = getStart(new Date());
    var week_end = new Date(week_start.getTime() + (6 * 24 * 60 * 60 * 1000));
    week_end.setUTCHours(23,59,59,999);
    var total_data = {"data": []};

    client.search({
        index: 'ivr',
        type: 'statuses',
        body: {
            "query": {
                "bool": {
                    "must": [
                        {
                            "match_all": {}
                        }
                    ],
                    "filter": [
                        {
                            "range": {
                                "created_at": {
                                    "gte": week_start,
                                    "lte": week_end
                                }
                            }
                        }
                    ]
                }
            }
        }
    }).then(function (resp) {
        var result = resp.hits.hits;
        if (result.length > 0) {
            var this_week_ungrouped = result.map(function (__obj) {
                return __obj._source
            });
            this_week_ungrouped.forEach(function(current) {
                var existing = total_data.data.filter(function(v, i) {
                    return v.campaign_id == current.campaign_id;
                });
                if (existing.length) {
                    var elem = existing[0];
                    elem.cdr_count = parseInt(elem.cdr_count) + parseInt(current.cdr_count);
                    elem.already_subbed_count = parseInt(elem.already_subbed_count) + parseInt(current.already_subbed_count);
                    elem.confirmation_count = parseInt(elem.confirmation_count) + parseInt(current.confirmation_count);
                    elem.failed_count = parseInt(elem.failed_count) + parseInt(current.failed_count);
                    elem.impression_count = parseInt(elem.impression_count) + parseInt(current.impression_count);
                    elem.insufficient_count = parseInt(elem.insufficient_count) + parseInt(current.insufficient_count);
                    elem.subscription_count = parseInt(elem.subscription_count) + parseInt(current.subscription_count);
                    elem.success_count = parseInt(elem.success_count) + parseInt(current.success_count);
                } else {
                    total_data.data.push(current);
                }
            });
        }
        res.send(JSON.stringify(total_data.data));
    });
});

router.get('/data/last', function (req, res, next) {

    var week_start = getStart(new Date());
    var last_start = new Date(week_start.getTime() - (7 * 24 * 60 * 60 * 1000));
    last_start.setUTCHours(0,0,0,0);
    var last_end = new Date(week_start.getTime() - (24 * 60 * 60 * 1000));
    last_end.setUTCHours(23,59,59,999);
    var total_data = {"data": []};

    client.search({
        index: 'ivr',
        type: 'statuses',
        body: {
            "query": {
                "bool": {
                    "must": [
                        {
                            "match_all": {}
                        }
                    ],
                    "filter": [
                        {
                            "range": {
                                "created_at": {
                                    "gte": last_start,
                                    "lte": last_end
                                }
                            }
                        }
                    ]
                }
            }
        }
    }).then(function (resp) {
        var result = resp.hits.hits;
        if (result.length > 0) {
            var data_ungrouped = result.map(function (__obj) {
                return __obj._source
            });
            data_ungrouped.forEach(function(current) {
                var existing = total_data.data.filter(function(v, i) {
                    return v.campaign_id == current.campaign_id;
                });
                if (existing.length) {
                    var elem = existing[0];
                    elem.cdr_count = parseInt(elem.cdr_count) + parseInt(current.cdr_count);
                    elem.already_subbed_count = parseInt(elem.already_subbed_count) + parseInt(current.already_subbed_count);
                    elem.confirmation_count = parseInt(elem.confirmation_count) + parseInt(current.confirmation_count);
                    elem.failed_count = parseInt(elem.failed_count) + parseInt(current.failed_count);
                    elem.impression_count = parseInt(elem.impression_count) + parseInt(current.impression_count);
                    elem.insufficient_count = parseInt(elem.insufficient_count) + parseInt(current.insufficient_count);
                    elem.subscription_count = parseInt(elem.subscription_count) + parseInt(current.subscription_count);
                    elem.success_count = parseInt(elem.success_count) + parseInt(current.success_count);
                } else {
                    total_data.data.push(current);
                }
            });
        }
        res.send(JSON.stringify(total_data));
    });
});

router.get('/data/month', function (req, res, next) {

    var date = new Date(), y = date.getFullYear(), m = date.getMonth();
    var start = new Date(y, m, 1);
    var end = new Date(y, m + 1, 0);
    start.setHours(1,0,0,0);
    end.setHours(24,59,59,999);
    var total_data = {"data": []};

    client.search({
        index: 'ivr',
        type: 'statuses',
        body: {
            "query": {
                "bool": {
                    "must": [
                        {
                            "match_all": {}
                        }
                    ],
                    "filter": [
                        {
                            "range": {
                                "created_at": {
                                    "gte": start,
                                    "lte": end
                                }
                            }
                        }
                    ]
                }
            }
        }
    }).then(function (resp) {
        var result = resp.hits.hits;
        if (result.length > 0) {
            var data_ungrouped = result.map(function (__obj) {
                return __obj._source
            });
            data_ungrouped.forEach(function(current) {
                var existing = total_data.data.filter(function(v, i) {
                    return v.campaign_id == current.campaign_id;
                });
                if (existing.length) {
                    var elem = existing[0];
                    elem.cdr_count = parseInt(elem.cdr_count) + parseInt(current.cdr_count);
                    elem.already_subbed_count = parseInt(elem.already_subbed_count) + parseInt(current.already_subbed_count);
                    elem.confirmation_count = parseInt(elem.confirmation_count) + parseInt(current.confirmation_count);
                    elem.failed_count = parseInt(elem.failed_count) + parseInt(current.failed_count);
                    elem.impression_count = parseInt(elem.impression_count) + parseInt(current.impression_count);
                    elem.insufficient_count = parseInt(elem.insufficient_count) + parseInt(current.insufficient_count);
                    elem.subscription_count = parseInt(elem.subscription_count) + parseInt(current.subscription_count);
                    elem.success_count = parseInt(elem.success_count) + parseInt(current.success_count);
                } else {
                    total_data.data.push(current);
                }
            });
        }
        res.send(JSON.stringify(total_data));
    });
});

router.get('/elasticsearch/data', function (req, res, next) {

    var day = new Date();
    day.setUTCHours(0, 0, 0, 0);
    var right_now = new Date();
    right_now.setUTCHours(23, 59, 59, 999);
    var total_data = {"today": [], "yesterday": [], "last_week": [], "this_week": [], "month": []};

    // today CDR records
    client.search({
        index: 'ivr',
        type: 'statuses',
        body: {
            // "query": {
            //     "filtered": {
            //         "query": {
            //             "match_all": {
            //             }
            //         },
            //         "filter": {
            //             "range": {
            //                 "created_at": {
            //                     "gte": day,
            //                     "lte": right_now
            //                 }
            //             }
            //         }
            //     }
            // }
            "query": {
                "bool": {
                    "must": [
                        {
                            "match_all": {}
                        }
                    ],
                    "filter": [
                        {
                            "range": {
                                "created_at": {
                                    "gte": day,
                                    "lte": right_now
                                }
                            }
                        }
                    ]
                }
            }
        }
    }).then(function (resp) {
        var result = resp.hits.hits;
        if (result.length > 0) {
            total_data.today = result.map(function (_obj) {
                return _obj._source
            });
        }

        // yesterday cdr records
        var yesterday_start = new Date(new Date().getTime() - (24 * 60 * 60 * 1000));
        yesterday_start.setUTCHours(0, 0, 0, 0);
        var yesterday_end = new Date(new Date().getTime() - (24 * 60 * 60 * 1000));
        yesterday_end.setUTCHours(23,59,59,999);
        client.search({
            index: 'ivr',
            type: 'statuses',
            body: {
                "query": {
                    "bool": {
                        "must": [
                            {
                                "match_all": {}
                            }
                        ],
                        "filter": [
                            {
                                "range": {
                                    "created_at": {
                                        "gte": yesterday_start,
                                        "lte": yesterday_end
                                    }
                                }
                            }
                        ]
                    }
                }
            }
        }).then(function (resp) {
            var yer_result = resp.hits.hits;
            if (yer_result.length > 0) {
                total_data.yesterday = yer_result.map(function (__obj) {
                    return __obj._source
                });
            }

            // this week cdr records
            var week_start = getStart(new Date());
            var week_end = new Date(week_start.getTime() + (6 * 24 * 60 * 60 * 1000));
            week_end.setUTCHours(23,59,59,999);
            client.search({
                index: 'ivr',
                type: 'statuses',
                body: {
                    "query": {
                        "bool": {
                            "must": [
                                {
                                    "match_all": {}
                                }
                            ],
                            "filter": [
                                {
                                    "range": {
                                        "created_at": {
                                            "gte": week_start,
                                            "lte": week_end
                                        }
                                    }
                                }
                            ]
                        }
                    }
                }
            }).then(function (resp) {
                var this_week_result = resp.hits.hits;
                if (this_week_result.length > 0) {

                    var this_week_ungrouped = this_week_result.map(function (__obj) {
                        return __obj._source
                    });
                    this_week_ungrouped.forEach(function(current) {
                        var existing = total_data.this_week.filter(function(v, i) {
                            return v.campaign_id == current.campaign_id;
                        });
                        if (existing.length) {
                            var elem = existing[0];
                            elem.cdr_count = parseInt(elem.cdr_count) + parseInt(current.cdr_count);
                            elem.already_subbed_count = parseInt(elem.already_subbed_count) + parseInt(current.already_subbed_count);
                            elem.confirmation_count = parseInt(elem.confirmation_count) + parseInt(current.confirmation_count);
                            elem.failed_count = parseInt(elem.failed_count) + parseInt(current.failed_count);
                            elem.impression_count = parseInt(elem.impression_count) + parseInt(current.impression_count);
                            elem.insufficient_count = parseInt(elem.insufficient_count) + parseInt(current.insufficient_count);
                            elem.subscription_count = parseInt(elem.subscription_count) + parseInt(current.subscription_count);
                            elem.success_count = parseInt(elem.success_count) + parseInt(current.success_count);
                        } else {
                            total_data.this_week.push(current);
                        }
                    });
                }

                // last week CDR records
                var last_start = new Date(week_start.getTime() - (7 * 24 * 60 * 60 * 1000));
                last_start.setUTCHours(0,0,0,0);
                var last_end = new Date(week_start.getTime() - (24 * 60 * 60 * 1000));
                last_end.setUTCHours(23,59,59,999);
                client.search({
                    index: 'ivr',
                    type: 'statuses',
                    body: {
                        "query": {
                            "bool": {
                                "must": [
                                    {
                                        "match_all": {}
                                    }
                                ],
                                "filter": [
                                    {
                                        "range": {
                                            "created_at": {
                                                "gte": last_start,
                                                "lte": last_end
                                            }
                                        }
                                    }
                                ]
                            }
                        }
                    }
                }).then(function (resp) {
                    var last_result = resp.hits.hits;
                    if (last_result.length > 0) {
                        var last_week_ungrouped = last_result.map(function (__obj) {
                            return __obj._source
                        });
                        last_week_ungrouped.forEach(function(current) {
                            var existing = total_data.last_week.filter(function(v, i) {
                                return v.campaign_id == current.campaign_id;
                            });
                            if (existing.length) {
                                var elem = existing[0];
                                elem.cdr_count = parseInt(elem.cdr_count) + parseInt(current.cdr_count);
                                elem.already_subbed_count = parseInt(elem.already_subbed_count) + parseInt(current.already_subbed_count);
                                elem.confirmation_count = parseInt(elem.confirmation_count) + parseInt(current.confirmation_count);
                                elem.failed_count = parseInt(elem.failed_count) + parseInt(current.failed_count);
                                elem.impression_count = parseInt(elem.impression_count) + parseInt(current.impression_count);
                                elem.insufficient_count = parseInt(elem.insufficient_count) + parseInt(current.insufficient_count);
                                elem.subscription_count = parseInt(elem.subscription_count) + parseInt(current.subscription_count);
                                elem.success_count = parseInt(elem.success_count) + parseInt(current.success_count);
                            } else {
                                total_data.last_week.push(current);
                            }
                        });
                    }

                    // this month CDR records
                    var date = new Date(), y = date.getFullYear(), m = date.getMonth();
                    var firstDay = new Date(y, m, 1);
                    var lastDay = new Date(y, m + 1, 0);
                    firstDay.setHours(1,0,0,0);
                    lastDay.setHours(24,59,59,999);
                    client.search({
                        index: 'ivr',
                        type: 'statuses',
                        body: {
                            "query": {
                                "bool": {
                                    "must": [
                                        {
                                            "match_all": {}
                                        }
                                    ],
                                    "filter": [
                                        {
                                            "range": {
                                                "created_at": {
                                                    "gte": firstDay,
                                                    "lte": lastDay
                                                }
                                            }
                                        }
                                    ]
                                }
                            }
                        }
                    }).then(function (resp) {
                        var month_result = resp.hits.hits;
                        if (month_result.length > 0) {
                            var month_ungrouped = month_result.map(function (__obj) {
                                return __obj._source
                            });
                            month_ungrouped.forEach(function(current) {
                                var existing = total_data.month.filter(function(v, i) {
                                    return v.campaign_id == current.campaign_id;
                                });
                                if (existing.length) {
                                    var elem = existing[0];
                                    elem.cdr_count = parseInt(elem.cdr_count) + parseInt(current.cdr_count);
                                    elem.already_subbed_count = parseInt(elem.already_subbed_count) + parseInt(current.already_subbed_count);
                                    elem.confirmation_count = parseInt(elem.confirmation_count) + parseInt(current.confirmation_count);
                                    elem.failed_count = parseInt(elem.failed_count) + parseInt(current.failed_count);
                                    elem.impression_count = parseInt(elem.impression_count) + parseInt(current.impression_count);
                                    elem.insufficient_count = parseInt(elem.insufficient_count) + parseInt(current.insufficient_count);
                                    elem.subscription_count = parseInt(elem.subscription_count) + parseInt(current.subscription_count);
                                    elem.success_count = parseInt(elem.success_count) + parseInt(current.success_count);
                                } else {
                                    total_data.month.push(current);
                                }
                            });
                        }
                        res.send(JSON.stringify(total_data));
                    });
                });
            });


        });
    });
});

router.post('/elasticsearch/campaign/path', function (req, res, next) {
    client.search({
        index: 'ivr',
        type: 'campaign',
        body: {
            "query": {
                "constant_score": {
                    "filter": {
                        "term": {
                            "play_path": req.body.path
                        }
                    }
                }
            }
        }
    }).then(function (resp) {
        if (resp.hits.hits.length > 0) {
            var result = resp.hits.hits[0]._source;
            return res.send(JSON.stringify({message: result}));
        }
    });
});

router.post('/record/filter', function (req, res, next) {

    var start = new Date(new Date(req.body.start));
    start.setUTCHours(0, 0, 0, 0);
    var end = new Date(req.body.end);
    end.setUTCHours(23, 59, 59, 999);
    client.search({
        index: "ivr",
        type: "statuses",
        body: {
            "query": {
                "bool": {
                    "must": [
                        {
                            "match_all": {}
                        }
                    ],
                    "filter": [
                        {
                            "range": {
                                "created_at": {
                                    "gte": start,
                                    "lte": end
                                }
                            }
                        }
                    ]
                }
            }
        }
    }).then(function (resp) {
        var result = resp.hits.hits;

        var data = result.map(function (_obj) {
            return _obj._source
        });

        var ar = groupBy(data, "campaign_name");

        res.setHeader('Content-Type', 'application/json');
        res.send(JSON.stringify({result: ar}));
    });
});

/* GET elastic listing. */
router.get('/', function (req, res, next) {
    res.send('respond with a resource');
});

module.exports = router;
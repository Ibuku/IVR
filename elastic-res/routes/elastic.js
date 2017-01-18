function getStart(d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day + (day == 0 ? -6:0);
    var x = new Date(d.setDate(diff));
    x.setUTCHours(0,0,0,0);
    return new Date(x);
}

var express = require('express');
var router = express.Router();

var redis = require("redis");
redis_client = redis.createClient();

redis_client.on("error", function (err) {
    console.log("Error " + err);
});

var elasticsearch = require('elasticsearch');

//connect to elastic search
var client = elasticsearch.Client({
    host: 'localhost:9200',
    log: 'trace'
});

//Search for campaign where this file_path matches

// var findByID = function (campaign_id) {
//     client.get({
//         index: 'ivr',
//         type: 'campagin',
//         id: campaign_id
//     }).then(function (resp) {
//         return resp.hits.hits[0]._id;
//     });
// };

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

// client.indices.putMapping({
//     index: 'ivr',
//     type: 'campaign',
//     body: {
//         "properties": {
//             "file_path": {
//                 "type": "string",
//                 "index": "not_analyzed"
//             }
//         }
//     }
// });

router.post('/campaign/retrieve', function (req, res, next) {
    var variable = req.body.file_path || null;
    client.search({
        index: 'ivr',
        type: 'campaign',
        body: {
            "query": {
                "constant_score": {
                    "filter": {
                        "term": {
                            "file_path": variable
                        }
                    }
                }
            }
        }
    }, function (err, resp, status) {
        res.setHeader('Content-Type', 'application/json');
        res.send(JSON.stringify({response: resp, error: err, status: status}));
    });
});

//Indexing into a type
router.post('/elasticsearch/:type/create', function (req, res, next) {

    if (req.params.type == "campaign") {
        var created = new Date(req.body.created_at);
        var updated = new Date(req.body.updated_at);
        var sd = new Date(req.body.start_date);
        var ed = new Date(req.body.end_date);
        created.toDateString
        updated.toDateString
        sd.toDateString
        ed.toDateString

        client.index({
            index: 'ivr',
            type: req.params.type,
            id: req.body.id,
            body: {
                "id": req.body.id,
                "name": req.body.name,
                "description": req.body.description,
                "username": req.body.username,
                "is_active": req.body.is_active,
                "file_path": req.body.file_path,
                "play_path": req.body.play_path,
                "value": req.body.value,
                "body": req.body.body,
                "created_at": created,
                "updated_at": updated,
                "start_date": sd,
                "end_date": ed
            }
        }, function (err, resp, status) {
            res.setHeader('Content-Type', 'application/json');
            res.send(JSON.stringify({response: resp, error: err, status: status}));
        });
    }
    else if (req.params.type == "cdr") {

        client.search({
            index: 'ivr',
            type: 'campaign',
            body: {
                "query": {
                    "constant_score": {
                        "filter": {
                            "term": {
                                "play_path": req.body.file_path
                            }
                        }
                    }
                }
            }
        }).then(function (resp) {
            if (resp.hits.hits.length > 0) {
                var campaign = resp.hits.hits[0]._source;
                var created = new Date();
                created.toDateString;

                client.index({
                    index: 'ivr',
                    id: req.body.uniqueid,
                    type: req.params.type,
                    body: {
                        "src": req.body.src,
                        "clid": req.body.clid,
                        "duration": req.body.duration,
                        "userfield": campaign.id,
                        "uniqueid": req.body.uniqueid,
                        "billsec": req.body.billsec,
                        "created_at": created,
                        "file_path": req.body.file_path,
                        "campaign_name": campaign.name,
                        "impression": false,
                        "is_subscribed": false,
                        "is_confirmed": false,
                        "is_successful": false,
                        "is_insufficient": false,
                        "has_failed": false
                    }
                }, function (err, resp, status) {
                    var status_id = new Date().toDateString().replace(/ /g, '') + '-' + campaign.id;
                    client.exists({
                        index: 'ivr',
                        type: 'statuses',
                        id: status_id
                    }, function (error, exists) {
                        if (exists == true) {
                            client.get({
                                index: 'ivr',
                                type: 'statuses',
                                id: status_id
                            }, function (error, response) {
                                client.update({
                                    index: 'ivr',
                                    type: 'statuses',
                                    id: status_id,
                                    body: {
                                        doc: {
                                            cdr_count: response._source.cdr_count + 1
                                        }
                                    }
                                }, function (error, response) {
                                })
                            });
                        } else {
                            client.index({
                                index: 'ivr',
                                type: 'statuses',
                                id: status_id,
                                body: {
                                    "campaign_id": campaign.id,
                                    "campaign_name": campaign.name,
                                    "created_at": created,
                                    "username": campaign.username,
                                    "cdr_count": 1,
                                    "impression_count": 0,
                                    "subscription_count": 0,
                                    "confirmation_count": 0,
                                    "insufficient_count": 0,
                                    "failed_count": 0,
                                    "success_count": 0
                                }
                            })
                        }
                    });
                    client.search({
                        index: 'ivr',
                        type: 'action',
                        body: {
                            "query": {
                                "constant_score": {
                                    "filter": {
                                        "term": {
                                            "campaign_id": campaign.id
                                        }
                                    }
                                }
                            }
                        }
                    }).then(function (resp) {
                        result = resp.hits.hits;
                        var data = result.map(function (_obj) {
                            return _obj._source;
                        });
                        for (i = 0; i < data.length; i++) {
                            var _source = data[i];
                            redis_client.hmset(req.body.uniqueid + ':' + _source.number, "value", _source.value, "body", _source.body, "number", _source.number, 'parameter', _source.parameter, function (err, res) {
                            });
                        }
                        res.setHeader('Content-Type', 'application/json');
                        res.send(JSON.stringify({response: resp, error: err}));
                    });
                });
            }
        });
    }
    else if (req.params.type == 'action') {
        client.index({
            index: 'ivr',
            type: req.params.type,
            id: req.body.id,
            body: {
                "id": req.body.id,
                "number": req.body.number,
                "campaign_id": req.body.campaign_id,
                "value": req.body.value,
                "body": req.body.body,
                "request": req.body.request,
                "parameter": req.body.parameter,
                "repeat_param": req.body.repeat_param,
                "confirm": req.body.confirm
            }
        }, function (err, resp, status) {
            res.setHeader('Content-Type', 'application/json');
            res.send(JSON.stringify({response: resp, error: err, status: status}));
        });
    }
    else if (req.params.type == 'settings') {
        client.index({
            index: 'ivr',
            type: req.params.type,
            id: req.body.id,
            body: {
                "id": req.body.id,
                "advert_limit": req.body.advert_limit,
                "incorrect_path": req.body.incorrect_path,
                "repeat_path": req.body.repeat_path,
                "confirmation_path": req.body.confirmation_path,
                "goodbye_path": req.body.goodbye_path,
                "selection_confirmation_path": req.body.selection_confirmation_path,
                "no_selection_path": req.body.no_selection_path,
                "wrong_path": req.body.wrong_path,
                "success_path": req.body.success_path,
                'default': req.body.default,
                'subscription_failure_path': req.body.subscription_failure_path,
                'continue_path': req.body.continue_path
            }
        }, function (err, resp, status) {
            res.setHeader('Content-Type', 'application/json');
            res.send(JSON.stringify({response: resp, error: err, status: status}));
        });
    }
});

router.post('/cdr/impression', function (req, res, next) {

    client.get({
        index: 'ivr',
        type: 'cdr',
        id: req.body.uniqueid
    }, function (err, resp) {
        client.update({
            index: 'ivr',
            type: 'cdr',
            id: req.body.uniqueid,
            body: {
                doc: {
                    impression: true
                }
            }
        }, function (errr, respose) {
            var campaign_id = resp._source.userfield;
            var status_id = new Date().toDateString().replace(/ /g, '') + '-' + campaign_id;
            client.get({
                index: 'ivr',
                type: 'statuses',
                id: status_id
            }, function (error, response) {
                client.update({
                    index: 'ivr',
                    type: 'statuses',
                    id: status_id,
                    body: {
                        doc: {
                            impression_count: response._source.impression_count + 1
                        }
                    }
                }, function (error, response) {
                    res.setHeader('Content-Type', 'application/json');
                    res.send(JSON.stringify({response: response, error: error}));
                })
            });
        });
    });
});

router.post('/cdr/subscribe', function (req, res, next) {

    client.get({
        index: 'ivr',
        type: 'cdr',
        id: req.body.uniqueid
    }, function (err, resp) {
        client.update({
            index: 'ivr',
            type: 'cdr',
            id: req.body.uniqueid,
            body: {
                doc: {
                    is_subscribed: true
                }
            }
        }, function (errr, respose) {
            var campaign_id = resp._source.userfield;
            var status_id = new Date().toDateString().replace(/ /g, '') + '-' + campaign_id;
            client.get({
                index: 'ivr',
                type: 'statuses',
                id: status_id
            }, function (error, response) {
                client.update({
                    index: 'ivr',
                    type: 'statuses',
                    id: status_id,
                    body: {
                        doc: {
                            subscription_count: response._source.subscription_count + 1
                        }
                    }
                }, function (error, response) {
                    res.setHeader('Content-Type', 'application/json');
                    res.send(JSON.stringify({response: response, error: error}));
                })
            });
        });
    });
});

router.post('/cdr/confirmation', function (req, res, next) {

    client.get({
        index: 'ivr',
        type: 'cdr',
        id: req.body.uniqueid
    }, function (err, resp) {
        client.update({
            index: 'ivr',
            type: 'cdr',
            id: req.body.uniqueid,
            body: {
                doc: {
                    is_confirmed: true
                }
            }
        }, function (errr, respose) {
            var campaign_id = resp._source.userfield;
            var status_id = new Date().toDateString().replace(/ /g, '') + '-' + campaign_id;
            client.get({
                index: 'ivr',
                type: 'statuses',
                id: status_id
            }, function (error, response) {
                client.update({
                    index: 'ivr',
                    type: 'statuses',
                    id: status_id,
                    body: {
                        doc: {
                            confirmation_count: response._source.confirmation_count + 1
                        }
                    }
                }, function (error, response) {
                    res.setHeader('Content-Type', 'application/json');
                    res.send(JSON.stringify({response: response, error: error}));
                })
            });
        });
    });
});

router.post('/cdr/success', function (req, res, next) {

    client.get({
        index: 'ivr',
        type: 'cdr',
        id: req.body.uniqueid
    }, function (err, resp) {
        client.update({
            index: 'ivr',
            type: 'cdr',
            id: req.body.uniqueid,
            body: {
                doc: {
                    is_successful: true
                }
            }
        }, function (errr, respose) {
            var campaign_id = resp._source.userfield;
            var status_id = new Date().toDateString().replace(/ /g, '') + '-' + campaign_id;
            client.get({
                index: 'ivr',
                type: 'statuses',
                id: status_id
            }, function (error, response) {
                client.update({
                    index: 'ivr',
                    type: 'statuses',
                    id: status_id,
                    body: {
                        doc: {
                            success_count: response._source.success_count + 1
                        }
                    }
                }, function (error, response) {
                    res.setHeader('Content-Type', 'application/json');
                    res.send(JSON.stringify({response: response, error: error}));
                })
            });
        });
    });
});

router.post('/cdr/insufficient', function (req, res, next) {

    client.get({
        index: 'ivr',
        type: 'cdr',
        id: req.body.uniqueid
    }, function (err, resp) {
        client.update({
            index: 'ivr',
            type: 'cdr',
            id: req.body.uniqueid,
            body: {
                doc: {
                    is_insufficient: true
                }
            }
        }, function (errr, respose) {
            var campaign_id = resp._source.userfield;
            var status_id = new Date().toDateString().replace(/ /g, '') + '-' + campaign_id;
            client.get({
                index: 'ivr',
                type: 'statuses',
                id: status_id
            }, function (error, response) {
                client.update({
                    index: 'ivr',
                    type: 'statuses',
                    id: status_id,
                    body: {
                        doc: {
                            insufficient_count: response._source.insufficient_count + 1
                        }
                    }
                }, function (error, response) {
                    res.setHeader('Content-Type', 'application/json');
                    res.send(JSON.stringify({response: response, error: error}));
                })
            });
        });
    });
});

router.post('/cdr/failed', function (req, res, next) {

    client.get({
        index: 'ivr',
        type: 'cdr',
        id: req.body.uniqueid
    }, function (err, resp) {
        client.update({
            index: 'ivr',
            type: 'cdr',
            id: req.body.uniqueid,
            body: {
                doc: {
                    has_failed: true
                }
            }
        }, function (errr, respose) {
            var campaign_id = resp._source.userfield;
            var status_id = new Date().toDateString().replace(/ /g, '') + '-' + campaign_id;
            client.get({
                index: 'ivr',
                type: 'statuses',
                id: status_id
            }, function (error, response) {
                client.update({
                    index: 'ivr',
                    type: 'statuses',
                    id: status_id,
                    body: {
                        doc: {
                            failed_count: response._source.failed_count + 1
                        }
                    }
                }, function (error, response) {
                    res.setHeader('Content-Type', 'application/json');
                    res.send(JSON.stringify({response: response, error: error}));
                })
            });
        });
    });
});

/*Number of campaign over a certain period*/
router.get('/no_of_campaign', function (req, res, next) {

    var sevenDays = new Date(new Date().getTime() - (6 * 24 * 60 * 60 * 1000));
    sevenDays.setUTCHours(0,0,0,0);
    var today = new Date();
    today.setUTCHours(23,59,59,59);
    client.search({
        index: "ivr",
        type: "statuses",
        body: {
            // "query": {
            //     "constant_score": {
            //         "filter": {
            //             "bool": {
            //                 "should": [
            //                     {
            //                         "range": {
            //                             "created_at": {
            //                                 "from": sevenDays,
            //                                 "to": today
            //                             }
            //                         }
            //                     }
            //                 ]
            //             }
            //         }
            //     }
            // }
            "query": {
                "filtered": {
                    "query": {
                        "match_all": {
                        }
                    },
                    "filter": {
                        "range": {
                            "created_at": {
                                "gte": sevenDays,
                                "lte": today
                            }
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

        var ar = groupBy(data, "campaign_name");

        res.setHeader('Content-Type', 'application/json');
        res.send(JSON.stringify({result: ar}));
    });
});

router.get('/campaign/:id/data', function (req, res, next) {

    var campaign_id = req.params.id;
    var sevenDays = new Date(new Date().getTime() - (6 * 24 * 60 * 60 * 1000));
    sevenDays.setUTCHours(0,0,0,0);
    var today = new Date(new Date().getTime() + (24 * 60 * 60 * 1000));
    today.setUTCHours(23,59,59,999);
    client.search({
        index: "ivr",
        type: "statuses",
        body: {
            // "query": {
            //     "constant_score": {
            //         "filter": {
            //             "bool": {
            //                 "should": [
            //                     {
            //                         "range": {
            //                             "created_at": {
            //                                 "from": sevenDays,
            //                                 "to": today
            //                             }
            //                         }
            //                     }
            //                 ]
            //             }
            //         }
            //     }
            // }
            "query": {
                "filtered": {
                    "query": {
                        "match_all": {
                        }
                    },
                    "filter": {
                        "range": {
                            "created_at": {
                                "gte": sevenDays,
                                "lte": today
                            }
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

//Campign impressions
router.get('/impressions/:campaign_id', function (req, res, next) {
    var sevenDays = new Date(new Date().getTime() - (6 * 24 * 60 * 60 * 1000));
    sevenDays.setHours(0,0,0,0);
    var today = new Date();
    today.setHours(0,0,0,0);
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
        type: req.params.type,
        body: {
            "src": req.body.src,
            "clid": req.body.clid,
            "duration": req.body.duration,
            "userfield": req.body.name,
            "uniqueid": req.body.uniqueid,
            "impression": impression,
            "billsec": req.body.billsec,
            "is_successful": false,
            "created_at": created
        }
    }, function (err, resp, status) {
        res.setHeader('Content-Type', 'application/json');
        res.send(JSON.stringify({response: true, error: err}));
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
            //     "constant_score": {
            //         "filter": {
            //             "range": {
            //                 "created_at": {
            //                     "gte": day,
            //                     "lte": right_now
            //                 }
            //             }
            //         }
            //
            //     }
            // }
            "query": {
                "filtered": {
                    "query": {
                        "match_all": {
                        }
                    },
                    "filter": {
                        "range": {
                            "created_at": {
                                "gte": day,
                                "lte": right_now
                            }
                        }
                    }
                }
            }
        }
    }).then(function (resp) {
        var result = resp.hits.hits;
        if (result.length > 0) {
            total_data.today = result.map(function (_obj) {
                return _obj._source
            });
            // total_data.today = groupBy(data, "userfield");
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
                // "query": {
                //     "constant_score": {
                //         "filter": {
                //             "range": {
                //                 "created_at": {
                //                     "gte": yesterday_start,
                //                     "lte": yesterday_end
                //                 }
                //             }
                //         }
                //
                //     }
                // }
                "query": {
                    "filtered": {
                        "query": {
                            "match_all": {
                            }
                        },
                        "filter": {
                            "range": {
                                "created_at": {
                                    "gte": yesterday_start,
                                    "lte": yesterday_end
                                }
                            }
                        }
                    }
                }
            }
        }).then(function (resp) {
            var yer_result = resp.hits.hits;
            if (yer_result.length > 0) {
                total_data.yesterday = yer_result.map(function (__obj) {
                    return __obj._source
                });
                // total_data.yesterday = groupBy(yer_data, "userfield");
            }

            // this week cdr records
            var week_start = getStart(new Date());
            var week_end = new Date(week_start.getTime() + (6 * 24 * 60 * 60 * 1000));
            week_end.setUTCHours(23,59,59,999);
            client.search({
                index: 'ivr',
                type: 'statuses',
                body: {
                    // "query": {
                    //     "constant_score": {
                    //         "filter": {
                    //             "range": {
                    //                 "created_at": {
                    //                     "gte": week_start,
                    //                     "lte": week_end
                    //                 }
                    //             }
                    //         }
                    //
                    //     }
                    // }
                    "query": {
                        "filtered": {
                            "query": {
                                "match_all": {
                                }
                            },
                            "filter": {
                                "range": {
                                    "created_at": {
                                        "gte": week_start,
                                        "lte": week_end
                                    }
                                }
                            }
                        }
                    }
                }
            }).then(function (resp) {
                var this_result = resp.hits.hits;
                if (this_result.length > 0) {
                    total_data.this_week = this_result.map(function (__obj) {
                        return __obj._source
                    });
                    // total_data.this_week = groupBy(this_data, "userfield");
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
                        // "query": {
                        //     "constant_score": {
                        //         "filter": {
                        //             "range": {
                        //                 "created_at": {
                        //                     "gte": last_start,
                        //                     "lte": last_end
                        //                 }
                        //             }
                        //         }
                        //
                        //     }
                        // }
                        "query": {
                            "filtered": {
                                "query": {
                                    "match_all": {
                                    }
                                },
                                "filter": {
                                    "range": {
                                        "created_at": {
                                            "gte": last_start,
                                            "lte": last_end
                                        }
                                    }
                                }
                            }
                        }
                    }
                }).then(function (resp) {
                    var last_result = resp.hits.hits;
                    if (last_result.length > 0) {
                        total_data.last_week = last_result.map(function (__obj) {
                            return __obj._source
                        });
                        // total_data.last_week = groupBy(last_data, "userfield");
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
                            // "query": {
                            //     "constant_score": {
                            //         "filter": {
                            //             "range": {
                            //                 "created_at": {
                            //                     "gte": firstDay,
                            //                     "lte": lastDay
                            //                 }
                            //             }
                            //         }
                            //
                            //     }
                            // }
                            "query": {
                                "filtered": {
                                    "query": {
                                        "match_all": {
                                        }
                                    },
                                    "filter": {
                                        "range": {
                                            "created_at": {
                                                "gte": firstDay,
                                                "lte": lastDay
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }).then(function (resp) {
                        var month_result = resp.hits.hits;
                        if (month_result.length > 0) {
                            total_data.month = month_result.map(function (__obj) {
                                return __obj._source
                            });
                            // total_data.month = groupBy(month_data, "userfield");
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


// router.get('/elasticsearch/:campaign_id/filter', function (req, res, next) {
//     //impression_count =sum all imression_count in campign_status that matchs campaign_id
//     ////success_count =sum all imression_count in campign_status that matchs campaign_id
//     //cdr_count = (all cdrs whos uniqueid matches campaign_id).count
//     //Expected parameters start_date and end_date
//     //A json encoded response
//     //B two arrays
//     // groupby date
//     //  1. key: today, value = arrayOf
//     /*{
//      {
//      "start_date": {"impressions_count": integer, "success_count": integer, "cdr_count": integer}
//      },
//      {
//      "end_date": {"impressions_count": integer, "success_count": integer, "cdr_count": integer}
//      }
//      }
//
//      */
//     //  2. key: yesterday, value = arrayOf
//     /*
//
//      */
//
//     var startDate = new Date(req.body.start_date);
//     var endDate = new Date(req.body.end_date);
//     startDate.toDateString
//     endDate.toDateString
//
//     var ivrDataFilterStartSate = new IvrDataFilter(req.params.campaign_id, today);
//     var ivrDataFilterEndDate = new IvrDataFilter(req.params.campaign_id, yesterday);
//
//     var impression_count_start_date = ivrDataFilterStartSate.getImpressionCount();
//     var success_count_start_date = ivrDataFilterStartSate.getSuccessCount();
//
//     var impression_count_end_date = ivrDataFilterEndDate.getImpressionCount();
//     var success_count_end_date = ivrDataFilterEndDate.getSuccessCount();
//
//     var cdr_count_start_date = ivrDataFilterStartSate.getCdrCount;
//     var cdr_count_end_date = ivrDataFilterEndDate.getCdrCount;
// });
//
// router.post('/elasticsearch/:type/:id/delete', function (req, res, next) {
//
//     res.setHeader('Content-Type', 'application/json');
//
//     client.delete({
//         index: 'ivr',
//         type: req.params.type,
//         id: req.params.id
//     }, function (error, response) {
//         if (error) {
//             return next(res.send(JSON.stringify({message: error})));
//         }
//
//         return res.send(JSON.stringify({message: response}));
//     });
// });


var groupBy = function (xs, key) {
    return xs.reduce(function (rv, x) {
            (rv[x[key]] = rv[x[key]] || []).push(x);
            return rv;
        }, {}
    );
};

/* GET elastic listing. */
router.get('/', function (req, res, next) {
    res.send('respond with a resource');
});


// function IvrDataFilter(search_date) {
//     this.search_date = search_date;
//
//     function searchWithId(id_field, date_field, type) {
//         client.search({
//             index: 'ivr',
//             type: type,
//             body: {
//                 "query": {
//                     "constant_score": {
//                         "filter": {
//                             "bool": {
//                                 "must": [
//                                     {
//                                         "term": {
//                                             date_field: this.search_date
//                                         }
//                                     }
//                                 ],
//                                 "should": [
//                                     {
//                                         "term": {
//                                             date_field: this.search_date
//                                         }
//                                     }
//                                 ]
//                             }
//                         }
//                     }
//                 }
//             }
//         }).then(function (resp) {
//             return resp.hits.hits;
//         });
//     }
//
//     function searchIvrTypePerDate(which_date, which_type) {
//
//         client.search({
//             index: 'ivr',
//             type: which_type,
//             body: {
//                 "query": {
//                     "constant_score": {
//                         "filter": {
//                             "term": {
//                                 created_at: which_type
//                             }
//                         }
//                     }
//                 }
//             }
//         }).then(function (resp) {
//             return resp.hits.hits;
//         })
//     }
//
//     this.searchCampaignStatusByDate = searchIvrTypePerDate(search_date, "statuses");
//
//     this.searchCDRByDate = searchIvrTypePerDate(search_date, "cdr");
//
//     this.getImpressionCount = function () {
//
//         var campaignResult = searchWithId("campaign_id", "created_at", "statuses");
//
//         var imp = campaignResult.map(function (val) {
//             return val.impression_count;
//         })
//         var sum = imp.reduce(function (prev, current) {
//             return prev + current;
//         });
//
//         return sum;
//     }
//
//     this.getSuccessCount = function () {
//
//         var campaignResult = searchWithId("campaign_id", "created_at", "statuses");
//
//         var imp = campaignResult.map(function (val) {
//             return val.success_count;
//         })
//         var sum = imp.reduce(function (prev, current) {
//             return prev + current;
//         });
//
//         return sum;
//     }
//
//     this.getCdrCount = searchWithId("uniqueid", "start", "cdr").length;
// }


module.exports = router;
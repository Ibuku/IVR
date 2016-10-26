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
                var impression = false;

                if (parseInt(req.body.duration) == parseInt(req.body.billsec)) {
                    impression = true;
                }

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
                        "impression": impression,
                        "billsec": req.body.billsec,
                        "is_successful": false,
                        "created_at": created,
                        "file_path": req.body.file_path,
                        "campaign_name": campaign.name
                        // "accountcode": req.body.accountcode,
                        // "dst": req.body.dst,
                        // "dcontext": req.body.dcontext,
                        // "channel": req.body.channel,
                        // "dstchannel": req.body.dstchannel,
                        // "start": req.body.start,
                        // "answer": req.body.answer,
                        // "end": req.body.end,
                        // "disposition": req.body.disposition,
                        //custom fields that need to be updated in db
                    }
                }, function (err, resp, status) {
                    var status_id = new Date().toDateString().replace(/ /g, '') + '-' + campaign.id;
                    client.exists({
                        index: 'ivr',
                        type: 'statuses',
                        id: status_id
                    }, function (error, exists) {
                        if (exists == true) {
                            // client.bulk({
                            //     body: [
                            //         {update: {_index: 'ivr', _type: 'statuses', _id: status_id}},
                            //         {script: 'ctx._source.cdr_count += 1'},
                            //
                            //         {update: {_index: 'ivr', _type: 'statuses', _id: status_id}},
                            //         {script: 'ctx._source.impressions_count += count'}
                            //     ]
                            // }, function (error, response) {
                            //     res.setHeader('Content-Type', 'application/json');
                            //     res.send(JSON.stringify({response: response, error: error}));
                            // })
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
                                            cdr_count: response._source.cdr_count + 1,
                                            impression_count: response._source.impression_count + 1
                                        }
                                    }
                                }, function (error, response) {
                                    res.setHeader('Content-Type', 'application/json');
                                    res.send(JSON.stringify({response: response, error: error}));
                                })
                            });
                        } else {
                            client.index({
                                index: 'ivr',
                                type: 'statuses',
                                id: status_id,
                                body: {
                                    "campaign_id": campaign.id,
                                    "impression_count": impression ? 1: 0,
                                    "success_count": 0,
                                    "cdr_count": 1,
                                    "campaign_name": campaign.name,
                                    "created_at": created
                                }
                            })
                        }
                    });
                    redis_client.hmset(req.body.uniqueid, "value", campaign.value, "body", campaign.body, function (err, res) {
                    });
                    res.setHeader('Content-Type', 'application/json');
                    res.send(JSON.stringify({response: resp, error: err}));
                });
            }
        });
    } else {
    }

});

/*Number of campaign over a certain period*/
router.get('/no_of_campaign', function (req, res, next) {

    var sevenDays = new Date(new Date().getTime() - (7 * 24 * 60 * 60 * 1000));
    var today = new Date()
    sevenDays.toDateString
    today.toDateString
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

        var data = result.map(function (_obj) {
            return _obj._source
        });

        var ar = groupBy(data, "campaign_name");

        res.setHeader('Content-Type', 'application/json');
        res.send(JSON.stringify({result: ar}));
    });
});

//Campign impressions
router.get('/impressions/:campaign_id', function (req, res, next) {
    var sevenDays = new Date(new Date().getTime() - (7 * 24 * 60 * 60 * 1000));
    var today = new Date()
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

router.post('/cdr/success', function (req, res, next) {

    client.update({
        index: 'ivr',
        type: 'cdr',
        id: req.body.uniqueid,
        body: {
            doc: {
                is_successful: true
            }
        }
    }, function (error, response) {
        client.get({
            index: 'ivr',
            type: 'cdr',
            id: req.body.uniqueid
        }, function (error, response) {
            var campaign_id = response._source.userfield;
            var status_id = new Date().toDateString().replace(/ /g, '') + '-' + campaign.id;
            client.update({
                index: 'ivr',
                type: 'statuses',
                id: status_id,
                body: {
                    script: 'ctx._source.success_count += 1'
                }
            }, function (error, response) {
                res.setHeader('Content-Type', 'application/json');
                res.send(JSON.stringify({response: response, error: error}));
            })
        });
    })

});

router.post('/elasticsearch/:type/:id/update', function (req, res, next) {

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
                "name": req.body.name,
                "description": req.body.description,
                "username": req.body.username,
                "is_active": req.body.is_active,
                "file_path": req.body.file_path,
                "play_path": req.body.play_path,
                "created_at": created,
                "updated_at": updated,
                "start_date": sd,
                "end_date": ed
            }
        }, function (err, resp, status) {
            res.setHeader('Content-Type', 'application/json');
            res.send(JSON.stringify({response: resp, error: err, status: status}));
        });
    } else if (req.params.type == "statuses") {
        var created = new Date(req.body.created_at);
        var update = new Date(req.body.updated_at);
        created.toDateString;
        update.toDateString;
        client.index({
            index: 'ivr',
            type: req.params.type,
            id: req.body.id,
            body: {
                "campaign_id": req.body.campaign_id,
                "impressions_count": req.body.impressions_count,
                "success_count": req.body.success_count,
                "created_at": created,
                "updated_at": update
            }
        }, function (err, resp, status) {
            res.setHeader('Content-Type', 'application/json');
            res.send(JSON.stringify({response: resp, error: err, status: status}));
        });
    } else if (req.params.type == "cdr") {

        var campaign = findCampaignID(req.body.file_path);
        var created = new Date(req.body.created_at);
        created.toDateString;
        var impression = false;

        if (req.body.billsec > 25) {
            impression = true;
        }

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
                "impression": impression,
                "billsec": req.body.billsec,
                "is_successful": false,
                "created_at": created,
                "file_path": findCampaignID
                // "accountcode": req.body.accountcode,
                // "dst": req.body.dst,
                // "dcontext": req.body.dcontext,
                // "channel": req.body.channel,
                // "dstchannel": req.body.dstchannel,
                // "start": req.body.start,
                // "answer": req.body.answer,
                // "end": req.body.end,
                // "disposition": req.body.disposition,
                //custom fields that need to be updated in db
            }
        }, function (err, resp, status) {
            res.setHeader('Content-Type', 'application/json');
            res.send(JSON.stringify({response: resp, error: err}));
        });
    } else {
        // res.setHeader('Content-Type', 'application/json');
        // res.send(JSON.stringify({error: 'Wrong type, please provide a valid type'}));
    }

    // if (req.body.id == null || req.body.uniqueid == null) {
    //     res.setHeader('Content-Type', 'application/json');
    //     res.send(JSON.stringify({message: 'Missing parameters'}));
    // } else {
    //     // check or create index type
    //
    // }
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
        var result = resp.hits.hits;
        var data = result.map(function (_obj) {
            return _obj._source
        });
        var _data = groupBy(data, "campaign_name");
        return res.send(JSON.stringify({message: _data}));
    });
});


/*
 All capaign status for today and group by campaign_id
 calculate impression count and success count

 All cdr for today group by campaign_id
 ccdr

 Response - {
 "campaign_a" : {'today':
 { cdr_count': 100, 'impressions_count': 10, 'success_count': 20}
 },
 {'yesterday':
 { cdr_count': 100, 'impressions_count': 10, 'success_count': 20}
 }
 "campaign_b": {'cdr_count': 100, 'impressions_count': 10, 'success_count': 20}
 }
 */

// router.get('/groupby', function (req, res, next) {
//
//
// });
//
// function queryFilter(_type, start_date, end_date, key) {
//     client.search({
//         index: 'ivr',
//         type: _type,
//         body: {
//             "query": {
//                 "constant_score": {
//                     "filter": {
//                         "range": {
//                             "created_at": {
//                                 "gte": start_date,
//                                 "lte": end_date
//                             }
//                         }
//                     }
//
//                 }
//             }
//         }
//     }, function (error, resp) {
//         return resp;
//         // var result = resp.hits.hits;
//         // var _data = result.map(function (_obj) {
//         //     return _obj._source
//         // });
//         // groupBy(_data, key);
//     });
// }

router.get('/elasticsearch/data', function (req, res, next) {
    // var ivrDataFilterToday = new IvrDataFilter(today);
    // var ivrDataFilterYesterday = new IvrDataFilter(yesterday);
    //
    // var todayStatusesGroupBy = groupBy(ivrDataFilterToday.searchCampaignStatusByDate(), "campaign_id");
    // var yesterdayStatusesGroupBy = groupBy(ivrDataFilterYesterday.searchCampaignStatusByDate(), "campaign_id");

    // var todayCDRgroup = groupBy(ivrDataFilterToday.searchCDRByDate(), "userfield");
    // var yesterdayCDRGroupBy = groupBy(ivrDataFilterYesterday.searchCDRByDate(), "userfield");

    var day = new Date();
    day.setHours(0, 0, 0, 0);
    var right_now = new Date();

    client.search({
        index: 'ivr',
        type: 'statuses',
        body: {
            "query": {
                "constant_score": {
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
            var data = result.map(function (_obj) {
                return _obj._source
            });
            var todayCDR = groupBy(data, "campaign_name");
        }
        else {
            todayCDR = []
        }

        var yesterday = new Date(new Date().getTime() - (24 * 60 * 60 * 1000));
        client.search({
            index: 'ivr',
            type: 'statuses',
            body: {
                "query": {
                    "constant_score": {
                        "filter": {
                            "range": {
                                "created_at": {
                                    "gte": yesterday,
                                    "lte": day
                                }
                            }
                        }

                    }
                }
            }
        }).then(function (resp) {
            var yer_result = resp.hits.hits;
            if (yer_result.length > 0) {
                var yer_data = yer_result.map(function (__obj) {
                    return __obj._source
                });
                var yesterdayCDR = groupBy(yer_data, "campaign_name");
            }
            else {
                yesterdayCDR = []
            }
            res.setHeader('Content-Type', 'application/json');
            return next(res.send(JSON.stringify({today: todayCDR, yesterday: yesterdayCDR})));
        });
        res.setHeader('Content-Type', 'application/json');
        return next(res.send(JSON.stringify({today: todayCDR, yesterday: yesterdayCDR})));
    });
});

// router.get('/elasticsearch/:campaign_id/data', function (req, res, next) {
//     var yesterday = new Date(new Date().getTime() - (1 * 24 * 60 * 60 * 1000));
//     var today = new Date()
//     yesterday.toDateString
//     today.toDateString

//     var ivrDataFilterToday = new IvrDataFilter(req.params.campaign_id, today);
//     var ivrDataFilterYesterday = new IvrDataFilter(req.params.campaign_id, yesterday);

//     var impression_count_today = ivrDataFilterToday.getImpressionCount();
//     var success_count_today = ivrDataFilterToday.getSuccessCount();

//     var impression_count_yesterday = ivrDataFilterYesterday.getImpressionCount();
//     var success_count_yesterday = ivrDataFilterYesterday.getSuccessCount();

//     var cdr_count_today = ivrDataFilterToday.getCdrCount;
//     var cdr_count_yesterday = ivrDataFilterYesterday.getCdrCount;

//     var result = [
//          {
//             impression_count : impression_count_today,
//             success_count : success_count_today,
//             cdr_count : cdr_count_today
//         },

//         {
//             impression_count: impression_count_yesterday,
//             success_count: success_count_yesterday,
//             cdr_count: cdr_count_yesterday
//         }
//     ]

//     return next(res.send(JSON.stringify({message: result})));


//     //A json encoded response
//     //B two arrays
//     //  1. key: today, value = arrayOf
//     /*{
//      {
//      "today": {"impressions_count": integer, "success_count": integer, "cdr_count": integer}
//      },
//      {
//      "yesterday": {"impressions_count": integer, "success_count": integer, "cdr_count": integer}
//      }
//      }

//      */
//     //  2. key: yesterday, value = arrayOf
//     /*

//      */
// });

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


router.get('/elasticsearch/:campaign_id/filter', function (req, res, next) {
    //impression_count =sum all imression_count in campign_status that matchs campaign_id
    ////success_count =sum all imression_count in campign_status that matchs campaign_id
    //cdr_count = (all cdrs whos uniqueid matches campaign_id).count
    //Expected parameters start_date and end_date
    //A json encoded response
    //B two arrays
    // groupby date
    //  1. key: today, value = arrayOf
    /*{
     {
     "start_date": {"impressions_count": integer, "success_count": integer, "cdr_count": integer}
     },
     {
     "end_date": {"impressions_count": integer, "success_count": integer, "cdr_count": integer}
     }
     }

     */
    //  2. key: yesterday, value = arrayOf
    /*

     */

    var startDate = new Date(req.body.start_date);
    var endDate = new Date(req.body.end_date);
    startDate.toDateString
    endDate.toDateString

    var ivrDataFilterStartSate = new IvrDataFilter(req.params.campaign_id, today);
    var ivrDataFilterEndDate = new IvrDataFilter(req.params.campaign_id, yesterday);

    var impression_count_start_date = ivrDataFilterStartSate.getImpressionCount();
    var success_count_start_date = ivrDataFilterStartSate.getSuccessCount();

    var impression_count_end_date = ivrDataFilterEndDate.getImpressionCount();
    var success_count_end_date = ivrDataFilterEndDate.getSuccessCount();

    var cdr_count_start_date = ivrDataFilterStartSate.getCdrCount;
    var cdr_count_end_date = ivrDataFilterEndDate.getCdrCount;
});

router.post('/elasticsearch/:type/:id/delete', function (req, res, next) {

    res.setHeader('Content-Type', 'application/json');

    client.delete({
        index: 'ivr',
        type: req.params.type,
        id: req.params.id
    }, function (error, response) {
        if (error) {
            return next(res.send(JSON.stringify({message: error})));
        }

        return res.send(JSON.stringify({message: response}));
    });
});


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


// var ivrSearch = {
//
// }

function IvrDataFilter(search_date) {
    this.search_date = search_date;

    function searchWithId(id_field, date_field, type) {
        client.search({
            index: 'ivr',
            type: type,
            body: {
                "query": {
                    "constant_score": {
                        "filter": {
                            "bool": {
                                "must": [
                                    {
                                        "term": {
                                            date_field: this.search_date
                                        }
                                    }
                                ],
                                "should": [
                                    {
                                        "term": {
                                            date_field: this.search_date
                                        }
                                    }
                                ]
                            }
                        }
                    }
                }
            }
        }).then(function (resp) {
            return resp.hits.hits;
        });
    }

    function searchIvrTypePerDate(which_date, which_type) {

        client.search({
            index: 'ivr',
            type: which_type,
            body: {
                "query": {
                    "constant_score": {
                        "filter": {
                            "term": {
                                created_at: which_type
                            }
                        }
                    }
                }
            }
        }).then(function (resp) {
            return resp.hits.hits;
        })
    }

    this.searchCampaignStatusByDate = searchIvrTypePerDate(search_date, "statuses");

    this.searchCDRByDate = searchIvrTypePerDate(search_date, "cdr");

    this.getImpressionCount = function () {

        var campaignResult = searchWithId("campaign_id", "created_at", "statuses");

        var imp = campaignResult.map(function (val) {
            return val.impressions_count;
        })
        var sum = imp.reduce(function (prev, current) {
            return prev + current;
        });

        return sum;
    }

    this.getSuccessCount = function () {

        var campaignResult = searchWithId("campaign_id", "created_at", "statuses");

        var imp = campaignResult.map(function (val) {
            return val.success_count;
        })
        var sum = imp.reduce(function (prev, current) {
            return prev + current;
        });

        return sum;
    }

    this.getCdrCount = searchWithId("uniqueid", "start", "cdr").length;
}


module.exports = router;
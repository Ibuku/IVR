var app = angular.module('mainApp', ['ngWebAudio']);

app.config(function ($interpolateProvider) {
    $interpolateProvider.startSymbol('[[').endSymbol(']]');
});

app.directive('ngFileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            var model = $parse(attrs.ngFileModel);
            var isMultiple = attrs.multiple;
            var modelSetter = model.assign;
            element.bind('change', function () {
                var values = [];
                angular.forEach(element[0].files, function (item) {
                    var value = {
                        // File Name
                        name: item.name,
                        //File Size
                        size: item.size,
                        //File URL to view
                        url: URL.createObjectURL(item),
                        // File Input Value
                        _file: item
                    };
                    values.push(value);
                });
                scope.$apply(function () {
                    if (isMultiple) {
                        modelSetter(scope, values);
                    } else {
                        modelSetter(scope, values[0]);
                    }
                });
            });
        }
    };
}]);

app.controller('UploadController', function ($scope, $location) {
    $scope.files = [];
    $scope.upload = function () {
        $.post($location.$$absUrl,
            $scope.files, function (data, status) {
                console.log(data);
                console.log(status);
            });

    };
});

app.controller('FileCtrl', function ($scope, WebAudio, $location) {

    $scope.init = function (data, username) {

        $scope.username = username;
        $scope.etisalat = false;
        $scope.tm30 = false;
        $scope.all = false;
        $scope.audio = null;

        if (username == 'etisalat') {
            $scope.etisalat = true;
        }
        else if (username == 'tm30') {
            $scope.tm30 = true;
        }
        else {
            $scope.all = true
        }

        $scope.data_bank = data;

        if (username != 'all') {
            $scope.data = $scope.data_bank.filter(function (value) {
                return value.username == $scope.username;
            });
        }
        else {
            $scope.data = data;
        }
    };

    $scope.changeActive = function (value) {
        $scope.username = value;
        $scope.etisalat = false;
        $scope.tm30 = false;
        $scope.all = false;

        if (value == 'etisalat') {
            $scope.etisalat = true;
        }
        else if (value == 'tm30') {
            $scope.tm30 = true;
        }
        else {
            $scope.all = true
        }

        if (value != 'all') {
            $scope.data = $scope.data_bank.filter(function (value) {
                return value.username == $scope.username;
            });
        }
        else {
            $scope.data = $scope.data_bank;
        }
    };

    $scope.clean = function (file_path) {
        var url= file_path;
        var parameter_Start_index=url.indexOf('/files');
        return url.substring(parameter_Start_index);
    };

    $scope.deleteFile = function (file_id) {
        var choice = window.confirm('Deleting this file will deactivate any campaign associated to this file.');
        if (choice) {
            $.post('/file/' + file_id + '/delete', {}, function (data, status) {
                location.href = '/file';
            });
        }
    };

    $scope.deactivateCampaign = function (campaign_id) {
        var choice = window.confirm('Are you sure you want to continue.');
        if (choice) {
            $.post('/campaign/' + campaign_id + '/deactivate', {}, function (data, status) {
                location.href = '/campaigns';
            });
        }
    };

});


app.controller('HomeController', function ($scope, $http, $timeout, $q) {

    var sum = function(items, prop){
        return items.reduce( function(a, b){
            return a + b[prop];
        }, 0);
    };

    var load_data = function () {
        $timeout(function () {
            $http({
                method: 'GET',
                url: '/dashboard'
            }).success(function successCallback(response) {

                Object.keys(response.today).map(function (key) {
                    // $scope.data_bank.today.push(response.today[key][0]);
                    // $scope.data_bank.totalToday += response.today[key][0].cdr_count;
                    // $scope.data_bank.impressionToday += response.today[key][0].impression_count;
                    $scope.data_bank.today.push(response.today[key]);
                    $scope.data_bank.totalToday += response.today[key].cdr_count;
                    $scope.data_bank.impressionToday += response.today[key].impression_count;
                });

                Object.keys(response.yesterday).map(function (key) {
                    // $scope.data_bank.yesterday.push(response.yesterday[key][0]);
                    // $scope.data_bank.totalYday += response.yesterday[key][0].cdr_count;
                    $scope.data_bank.yesterday.push(response.yesterday[key]);
                    $scope.data_bank.totalYday += response.yesterday[key].cdr_count;
                });

                Object.keys(response.this_week).map(function (key) {
                    $scope.data_bank.this_week.push(response.this_week[key]);
                    $scope.data_bank.totalTWk += response.this_week[key].cdr_count;
                    // $scope.data_bank.this_week.push(response.this_week[key][0]);
                    // $scope.data_bank.totalTWk += response.this_week[key][0].cdr_count;
                });

                Object.keys(response.last_week).map(function (key) {
                    $scope.data_bank.last_week.push(response.last_week[key]);
                    $scope.data_bank.totalLWk += response.last_week[key].cdr_count;
                    // $scope.data_bank.last_week.push(response.last_week[key][0]);
                    // $scope.data_bank.totalLWk += response.last_week[key][0].cdr_count;
                });

                Object.keys(response.month).map(function (key) {
                    $scope.data_bank.month.push(response.month[key]);
                    $scope.data_bank.totalMonth += response.month[key].cdr_count;
                    // $scope.data_bank.month.push(response.month[key][0]);
                    // $scope.data_bank.totalMonth += response.month[key][0].cdr_count;
                });

                if ($scope.username != 'all') {
                    $scope.filtered_data.today = $scope.data_bank.today.filter(function (value) {
                        return value.username == $scope.username;
                    });

                    // $scope.filtered_data.totalToday += $scope.filtered_data.today[key][0].cdr_count;
                    // $scope.filtered_data.impressionToday += $scope.filtered_data.today[key][0].impression_count;
                    //
                    // $scope.filtered_data.yesterday = $scope.data_bank.yesterday.filter(function (value) {
                    //     return value.username == $scope.username;
                    // });
                    // $scope.filtered_data.totalYday += $scope.filtered_data.yesterday[key][0].cdr_count;
                    //
                    // $scope.filtered_data.this_week = $scope.data_bank.this_week.filter(function (value) {
                    //     return value.username == $scope.username;
                    // });
                    // $scope.filtered_data.totalTWk += $scope.filtered_data.this_week[key][0].cdr_count;
                    //
                    // $scope.filtered_data.last_week = $scope.data_bank.last_week.filter(function (value) {
                    //     return value.username == $scope.username;
                    // });
                    // $scope.filtered_data.totalLWk += $scope.filtered_data.last_week[key][0].cdr_count;
                    //
                    // $scope.filtered_data.month = $scope.data_bank.month.filter(function (value) {
                    //     return value.username == $scope.username;
                    // });
                    // $scope.filtered_data.totalMonth += $scope.filtered_data.month[key][0].cdr_count;

                    $scope.filtered_data.totalToday += $scope.filtered_data.today[key].cdr_count;
                    $scope.filtered_data.impressionToday += $scope.filtered_data.today[key].impression_count;

                    $scope.filtered_data.yesterday = $scope.data_bank.yesterday.filter(function (value) {
                        return value.username == $scope.username;
                    });
                    $scope.filtered_data.totalYday += $scope.filtered_data.yesterday[key].cdr_count;

                    $scope.filtered_data.this_week = $scope.data_bank.this_week.filter(function (value) {
                        return value.username == $scope.username;
                    });
                    $scope.filtered_data.totalTWk += $scope.filtered_data.this_week[key].cdr_count;

                    $scope.filtered_data.last_week = $scope.data_bank.last_week.filter(function (value) {
                        return value.username == $scope.username;
                    });
                    $scope.filtered_data.totalLWk += $scope.filtered_data.last_week[key].cdr_count;

                    $scope.filtered_data.month = $scope.data_bank.month.filter(function (value) {
                        return value.username == $scope.username;
                    });
                    $scope.filtered_data.totalMonth += $scope.filtered_data.month[key].cdr_count;
                }
                else {
                    $scope.filtered_data = $scope.data_bank;
                }

            }).error(function errorCallback(err) {
                location.href = '/logout';
            });
        }, 10);
    };

    var startParallel = function () {
        $q.all([load_data()]).then(
            function (successResult) { // execute this if ALL promises are resolved (successful)
            }, function (failureReason) { // execute this if any promise is rejected (fails) - we don't have any reject calls in this demo
                // $scope.overallStatus = 'Failed: ' + failureReason;
                location.href = '/logout';
            }
        );
    };

    $scope.init = function (data, active, username) {

        $scope.response_data = {"data": []};
        $scope.data_bank = {"today": [], "yesterday": [], "totalToday": 0, "totalYday": 0, "impressionToday": 0,
            "this_week": [], "last_week": [], "month": [], "totalLWk": 0, "totalTWk": 0, "totalMonth": 0};
        $scope.filtered_data = {"today": [], "yesterday": [], "totalToday": 0, "totalYday": 0, "impressionToday": 0, "totalLWk": 0, "totalTWk": 0, "totalMonth": 0};

        $scope.username = username;

        $scope.campaigns_bank = data;

        if (username != 'all') {
            $scope.campaigns = $scope.campaigns_bank.filter(function (value) {
                return value.username == $scope.username;
            });
        }
        else {
            $scope.campaigns = data;
        }

        $scope.active_bank = active;
        $scope.active_campaigns = $.extend( [], $scope.active_bank );

        startParallel();
    };

    $scope.changeActive = function (value) {

        $timeout(function () {
            $scope.username = value;

            var copy = $.extend( [], $scope.data_bank );
            var campaigns_copy = $.extend( [], $scope.campaigns_bank );

            if (value != 'all') {
                $scope.campaigns = campaigns_copy.filter(function (value) {
                    return value.username == $scope.username;
                });

                $scope.filtered_data.today = copy.today.filter(function (value) {
                    return value.username == $scope.username;
                });

                $scope.filtered_data.totalToday = sum($scope.filtered_data.today, 'cdr_count');

                $scope.filtered_data.impressionToday = sum($scope.filtered_data.today, 'impression_count');

                $scope.filtered_data.yesterday = copy.yesterday.filter(function (value) {
                    return value.username == $scope.username;
                });
                $scope.filtered_data.totalYday = sum($scope.filtered_data.yesterday, 'cdr_count');

                $scope.filtered_data.this_week = copy.this_week.filter(function (value) {
                    return value.username == $scope.username;
                });
                $scope.filtered_data.totalTWk = sum($scope.filtered_data.this_week, 'cdr_count');

                $scope.filtered_data.last_week = copy.last_week.filter(function (value) {
                    return value.username == $scope.username;
                });
                $scope.filtered_data.totalLWk = sum($scope.filtered_data.last_week, 'cdr_count');

                $scope.filtered_data.month = copy.month.filter(function (value) {
                    return value.username == $scope.username;
                });
                $scope.filtered_data.totalMonth = sum($scope.filtered_data.month, 'cdr_count');

                var active_copy = $.extend([], $scope.active_bank);
                $scope.active_campaigns = active_copy.filter(function (val) {
                    return val.username == $scope.username;
                });
            }
            else {
                $scope.campaigns = $.extend( [], $scope.campaigns_bank );
                $scope.filtered_data = $.extend( [], $scope.data_bank );
                $scope.filtered_data.totalToday = sum($scope.filtered_data.today, 'cdr_count');
                $scope.filtered_data.impressionToday = sum($scope.filtered_data.today, 'impression_count');
                $scope.filtered_data.totalYday = sum($scope.filtered_data.yesterday, 'cdr_count');
                $scope.filtered_data.totalTWk = sum($scope.filtered_data.this_week, 'cdr_count');
                $scope.filtered_data.totalLWk = sum($scope.filtered_data.last_week, 'cdr_count');
                $scope.filtered_data.totalMonth = sum($scope.filtered_data.month, 'cdr_count');
                $scope.active_campaigns = $.extend([], $scope.active_bank);
            }
        }, 2);
    };
});

app.controller("ReportsController", function ($scope) {

    function buildData(data) {
        return {
            title: {
                text: data.text,
                x: -20 //center
            },
            subtitle: {
                text: data.subtitle,
                x: -20
            },
            xAxis: {
                categories: data.categories
            },
            yAxis: {
                title: {
                    text: data.yaxis_text
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: ''
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: data.series
        }
    }


    $scope.init = function () {

        var camp_data = {"data": [], "advert_data": [], "clicked_data": []};

        var sevenDays = new Date(new Date().getTime() - (6 * 24 * 60 * 60 * 1000));
        sevenDays.setHours(0,0,0,0);
        var weekday = ['Sun', 'Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat'];

        var today = new Date();
        today.setHours(23,59,59,59);
        var date_range = [today.getDay()];

        for (var i=1; i < 7; i++) {
            var x = new Date(new Date().getTime() - (i * 24 * 60 * 60 * 1000));
            x.setHours(0,0,0,0);
            date_range.push(x.getDay());
        }

        date_range.reverse();

        var week_map = [weekday[date_range[0]]];

        for (var j=1; j < 7; j++) {
            var y = date_range[j];
            var z = weekday[y];
            week_map.push(z);
        }

        $.get("/campaign/period", function (_data, status) {
            var data = JSON.parse(_data);
            Object.keys(data.result).map(function (key, index) {
                var temp_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                var temp = data.result[key];
                temp.map(function (i, j) {
                    var pos = new Date(temp[j].created_at).getDay();
                    var b = date_range.indexOf(pos);
                    temp_object['data'][b] = temp[j].cdr_count;
                });

                camp_data.data.push(temp_object);
            });


            var cam_data = {
                "categories": week_map,
                "text": "Call Records over a week",
                "subtitle": "All campaigns",
                "yaxis_text": "Call Record",
                "series": camp_data.data
            };
            $('#camp').highcharts(buildData(cam_data));

            Object.keys(data.result).map(function (key, index) {
                var advert_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                var _object = data.result[key];
                _object.map(function (i, j) {
                    var pos = new Date(_object[j].created_at).getDay();
                    var b = date_range.indexOf(pos);
                    advert_object['data'][b] = _object[j].impression_count;
                });

                camp_data.advert_data.push(advert_object);
            });

            var advert_data = {
                "categories": week_map,
                "text": "Adverts played/Call Impressions over a week",
                "subtitle": "All campaigns",
                "yaxis_text": "Call Impressions",
                "series": camp_data.advert_data
            };
            $('#success').highcharts(buildData(advert_data));

            // clicked through rate
            Object.keys(data.result).map(function (key, index) {
                var clicked_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                var c_object = data.result[key];
                c_object.map(function (i, j) {
                    var pos = new Date(c_object[j].created_at).getDay();
                    var b = date_range.indexOf(pos);
                    clicked_object['data'][pos] = c_object[j].success_count;
                });

                camp_data.clicked_data.push(clicked_object);
            });

            var clicked_data = {
                "categories": week_map,
                "text": "Successful Conversions over a week",
                "subtitle": "All campaigns",
                "yaxis_text": "Successful Conversions",
                "series": camp_data.clicked_data
            };
            $('#clicked').highcharts(buildData(clicked_data));

            // $('#clicked').highcharts({
            //     title: {
            //         text: 'Click Through Rate Bar chat',
            //         x: -20 //center
            //     },
            //     subtitle: {
            //         text: 'ivr',
            //         x: -20
            //     },
            //     xAxis: {
            //         categories: ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun']
            //     },
            //     yAxis: {
            //         title: {
            //             text: 'Counts'
            //         },
            //         plotLines: [{
            //             value: 0,
            //             width: 1,
            //             color: '#808080'
            //         }]
            //     },
            //     tooltip: {
            //         valueSuffix: ''
            //     },
            //     legend: {
            //         layout: 'vertical',
            //         align: 'right',
            //         verticalAlign: 'middle',
            //         borderWidth: 0
            //     },
            //     series: [{
            //         name: 'Follow Through',
            //         data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2]
            //     }
            //     ]
            // });
        });


        // $.get("http://voice.atp-sevas.com:4043/api/elasticsearch/statuses/all", function (data, status) {
        //     // Object.keys(data.result).map(function(key, index) {
        //     //     $scope.response.data.push(data.result[key][0]);
        //     // });
        //     Object.keys(data.message).map(function (key, index) {
        //         var temp_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
        //         var temp = data.result[key];
        //         temp.map(function (i, j) {
        //             var pos = new Date(temp[j].created_at).getDay();
        //             temp_object['data'][pos] = temp[j].cdr_count;
        //         });
        //
        //         camp_data.data.push(temp_object);
        //     });
        //
        //     var cam_data = {
        //         "categories": ['Sun', 'Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat'],
        //         "text": "Call Records over a week",
        //         "subtitle": "All campaigns",
        //         "yaxis_text": "Call Record",
        //         "series": camp_data.data
        //     };
        //     $('#success').highcharts({
        //         chart: {
        //             type: 'bar'
        //         },
        //         title: {
        //             text: 'All Campaign Call Records'
        //         },
        //         subtitle: {
        //             text: ''
        //         },
        //         xAxis: {
        //             categories: Object.keys(data.message),
        //             title: {
        //                 text: null
        //             }
        //         },
        //         yAxis: {
        //             min: 0,
        //             title: {
        //                 text: 'Call Records',
        //                 align: 'high'
        //             },
        //             labels: {
        //                 overflow: 'justify'
        //             }
        //         },
        //         tooltip: {
        //             valueSuffix: ' thousands'
        //         },
        //         plotOptions: {
        //             bar: {
        //                 dataLabels: {
        //                     enabled: true
        //                 }
        //             }
        //         },
        //         legend: {
        //             layout: 'vertical',
        //             align: 'right',
        //             verticalAlign: 'top',
        //             x: -40,
        //             y: 80,
        //             floating: true,
        //             borderWidth: 1,
        //             backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
        //             shadow: true
        //         },
        //         credits: {
        //             enabled: false
        //         },
        //         series: [{
        //             name: 'Year 1800',
        //             data: [107, 31, 635, 203, 2]
        //         }]
        //     });
        // });
    };
});

app.controller("ReportController", function ($scope) {

    function buildData(data) {
        return {
            title: {
                text: data.text,
                x: -20 //center
            },
            subtitle: {
                text: data.subtitle,
                x: -20
            },
            xAxis: {
                categories: data.categories
            },
            yAxis: {
                title: {
                    text: data.yaxis_text
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: ''
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: data.series
        }
    }


    $scope.init = function (campaign_id) {

        var camp_data = {"data": [], "advert_data": [], "clicked_data": []};

        var sevenDays = new Date(new Date().getTime() - (6 * 24 * 60 * 60 * 1000));
        sevenDays.setHours(0,0,0,0);
        var weekday = ['Sun', 'Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat'];

        var today = new Date();
        today.setHours(23,59,59,59);
        var date_range = [today.getDay()];

        for (var i=1; i < 7; i++) {
            var x = new Date(new Date().getTime() - (i * 24 * 60 * 60 * 1000));
            x.setHours(0,0,0,0);
            date_range.push(x.getDay());
        }

        date_range.reverse();

        var week_map = [weekday[date_range[0]]];

        for (var j=1; j < 7; j++) {
            var y = date_range[j];
            var z = weekday[y];
            week_map.push(z);
        }

        $.get("/campaign/" + campaign_id + "/data", function (_data, status) {
            var data = JSON.parse(_data);

            var temp_object = {"name": data.result[0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
            var advert_object = {"name": data.result[0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
            var clicked_object = {"name": data.result[0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
            var temp = data.result;
            temp.map(function (i, j) {
                var pos = new Date(temp[j].created_at).getDay();
                var b = date_range.indexOf(pos);
                temp_object['data'][b] = temp[j].cdr_count;
                advert_object['data'][b] = temp[j].impression_count;
                clicked_object['data'][b] = temp[j].success_count;
            });
            camp_data.data.push(temp_object);
            camp_data.advert_data.push(advert_object);
            camp_data.clicked_data.push(clicked_object);

            var cam_data = {
                "categories": week_map,
                "text": "Call Records over a week",
                "subtitle": data.result[0].campaign_name,
                "yaxis_text": "Call Record",
                "series": camp_data.data
            };
            $('#camp').highcharts(buildData(cam_data));

            var advert_data = {
                "categories": week_map,
                "text": "Adverts played/Call Impressions over a week",
                "subtitle": "Campaign",
                "yaxis_text": "Call Impressions",
                "series": camp_data.advert_data
            };
            $('#success').highcharts(buildData(advert_data));

            var clicked_data = {
                "categories": week_map,
                "text": "Successful Conversions over a week",
                "subtitle": "Campaign",
                "yaxis_text": "Successful Conversions",
                "series": camp_data.clicked_data
            };
            $('#clicked').highcharts(buildData(clicked_data));
        });
    };
});
function sum(items, prop){
    return items.reduce( function(a, b){
        return a + b[prop];
    }, 0);
}

function clone(obj) {
    var copy;

    // Handle the 3 simple types, and null or undefined
    if (null == obj || "object" != typeof obj) return obj;

    // Handle Date
    if (obj instanceof Date) {
        copy = new Date();
        copy.setTime(obj.getTime());
        return copy;
    }

    // Handle Array
    if (obj instanceof Array) {
        copy = [];
        for (var i = 0, len = obj.length; i < len; i++) {
            copy[i] = clone(obj[i]);
        }
        return copy;
    }

    // Handle Object
    if (obj instanceof Object) {
        copy = {};
        for (var attr in obj) {
            if (obj.hasOwnProperty(attr)) copy[attr] = clone(obj[attr]);
        }
        return copy;
    }

    throw new Error("Unable to copy obj! Its type isn't supported.");
}

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

    // $scope.changeActive = function (value) {
    //     $scope.username = value;
    //     $scope.etisalat = false;
    //     $scope.tm30 = false;
    //     $scope.all = false;
    //
    //     if (value == 'etisalat') {
    //         $scope.etisalat = true;
    //     }
    //     else if (value == 'tm30') {
    //         $scope.tm30 = true;
    //     }
    //     else {
    //         $scope.all = true
    //     }
    //
    //     if (value != 'all') {
    //         $scope.data = $scope.data_bank.filter(function (value) {
    //             return value.username == $scope.username;
    //         });
    //     }
    //     else {
    //         $scope.data = $scope.data_bank;
    //     }
    // };

    $scope.changeActive = function () {

        if ($scope.username != 'all') {
            $scope.data = $scope.data_bank.filter(function (val) {
                return val.username == $scope.username;
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

    $scope.activateCampaign = function (campaign_id) {
        var choice = window.confirm('Are you sure you want to continue.');
        if (choice) {
            $.post('/campaign/' + campaign_id + '/activate', {}, function (data, status) {
                location.href = '/campaigns';
            });
        }
    };

});

app.controller('AccountCtrl', function ($scope) {

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

    $scope.deactivateAccount = function (account_id) {
        var choice = window.confirm('Are you sure you want to continue.');
        if (choice) {
            $.post('/accounts/' + account_id + '/deactivate', {}, function (data, status) {
                location.href = '/accounts';
            });
        }
    };

    $scope.activateCampaign = function (account_id) {
        var choice = window.confirm('Are you sure you want to continue.');
        if (choice) {
            $.post('/accounts/' + account_id + '/activate', {}, function (data, status) {
                location.href = '/accounts';
            });
        }
    };

});

app.controller('HomeController', function ($scope, $rootScope, $http, $timeout, $q) {

    var resetData = function () {
        // today
        $scope.filtered_data.today = [];
        $scope.data_bank.today.forEach(function (val) {
            if (val.username === $scope.username) { $scope.filtered_data.today.push(val);}
        });
        $scope.filtered_data.totalToday = sum($scope.filtered_data.today, 'cdr_count');
        $scope.filtered_data.impressionToday = sum($scope.filtered_data.today, 'impression_count');

        // yesterday
        $scope.filtered_data.yesterday = [];
        $scope.data_bank.yesterday.forEach(function (v) {
            if(v.username === $scope.username) { $scope.filtered_data.yesterday.push(v); }
        });
        $scope.filtered_data.totalYday = sum($scope.filtered_data.yesterday, 'cdr_count');

        // this week
        $scope.filtered_data.this_week = [];
        $scope.data_bank.this_week.filter(function (i) {
            if(i.username == $scope.username) { $scope.filtered_data.this_week.push(i)}
        });
        $scope.filtered_data.totalTWk = sum($scope.filtered_data.this_week, 'cdr_count');

        // last week
        $scope.filtered_data.last_week = [];

        $scope.data_bank.last_week.forEach(function (k) {
            if(k.username == $scope.username) { $scope.filtered_data.last_week.push(k) }
        });
        $scope.filtered_data.totalLWk = sum($scope.filtered_data.last_week, 'cdr_count');

        // month
        $scope.filtered_data.month = [];
        $scope.data_bank.month.forEach(function (j) {
            if(j.username == $scope.username) { $scope.filtered_data.month.push(j) }
        });
        $scope.filtered_data.totalMonth = sum($scope.filtered_data.month, 'cdr_count');
    };

    var load_data = function () {
        $timeout(function () {
            $http({
                method: 'GET',
                url: '/dashboard'
            }).success(function successCallback(response) {

                if (response) {
                    Object.keys(response.today).map(function (key) {
                        $scope.data_bank.today.push(response.today[key]);
                        $scope.data_bank.totalToday += response.today[key].cdr_count;
                        $scope.data_bank.impressionToday += response.today[key].impression_count;
                    });

                    Object.keys(response.yesterday).map(function (key) {
                        $scope.data_bank.yesterday.push(response.yesterday[key]);
                        $scope.data_bank.totalYday += response.yesterday[key].cdr_count;
                    });

                    Object.keys(response.this_week).map(function (key) {
                        $scope.data_bank.this_week.push(response.this_week[key]);
                        $scope.data_bank.totalTWk += response.this_week[key].cdr_count;
                    });

                    Object.keys(response.last_week).map(function (key) {
                        $scope.data_bank.last_week.push(response.last_week[key]);
                        $scope.data_bank.totalLWk += response.last_week[key].cdr_count;
                    });

                    Object.keys(response.month).map(function (key) {
                        $scope.data_bank.month.push(response.month[key]);
                        $scope.data_bank.totalMonth += response.month[key].cdr_count;
                    });

                    if ($scope.username != 'all') {
                        resetData();
                    }
                    else {
                        $scope.filtered_data = clone($scope.data_bank);
                    }
                }

            }).error(function errorCallback(err) {
                location.href = '/logout';
            });
        }, 10);
    };

    var load_today = function () {
        $timeout(function () {
            $http({
                method: 'GET',
                url: '/dashboard/today'
            }).success(function successCallback(response) {
                if (response) {
                    Object.keys(response.today).map(function (key) {
                        $scope.data_bank.today.push(response.today[key]);
                        $scope.data_bank.totalToday += response.today[key].cdr_count;
                        $scope.data_bank.impressionToday += response.today[key].impression_count;
                    });

                    if ($scope.username != 'all') {
                        $scope.filtered_data.today = $scope.data_bank.today.filter(function (value) {
                            return value.username == $scope.username;
                        });

                        $scope.filtered_data.totalToday += $scope.filtered_data.today[key].cdr_count;
                        $scope.filtered_data.impressionToday += $scope.filtered_data.today[key].impression_count;

                    }
                    else {
                        $scope.filtered_data = $scope.data_bank;
                    }
                }

            }).error(function errorCallback(err) {
                location.href = '/logout';
            });
        }, 20);
    };

    var load_yesterday = function () {
        $timeout(function () {
            $http({
                method: 'GET',
                url: '/dashboard/yesterday'
            }).success(function successCallback(response) {
                if (response) {
                    Object.keys(response.yesterday).map(function (key) {
                        $scope.data_bank.yesterday.push(response.yesterday[key]);
                        $scope.data_bank.totalYday += response.yesterday[key].cdr_count;
                    });

                    if ($scope.username != 'all') {

                        $scope.filtered_data.yesterday = $scope.data_bank.yesterday.filter(function (value) {
                            return value.username == $scope.username;
                        });

                        $scope.filtered_data.totalYday += $scope.filtered_data.yesterday[key].cdr_count;
                    }
                    else {
                        $scope.filtered_data = $scope.data_bank;
                    }
                }
            }).error(function errorCallback(err) {
                location.href = '/logout';
            });
        }, 20);
    };

    var load_last_week = function () {
        $timeout(function () {
            $http({
                method: 'GET',
                url: '/dashboard/last'
            }).success(function successCallback(response) {
                if (response) {
                    Object.keys(response.last_week).map(function (key) {
                        $scope.data_bank.last_week.push(response.last_week[key]);
                        $scope.data_bank.totalLWk += response.last_week[key].cdr_count;
                    });

                    if ($scope.username != 'all') {

                        $scope.filtered_data.last_week = $scope.data_bank.last_week.filter(function (value) {
                            return value.username == $scope.username;
                        });
                        $scope.filtered_data.totalLWk += $scope.filtered_data.last_week[key].cdr_count;
                    }
                    else {
                        $scope.filtered_data = $scope.data_bank;
                    }
                }
            }).error(function errorCallback(err) {
                location.href = '/logout';
            });
        }, 20);
    };

    var load_this_week = function () {
        $timeout(function () {
            $http({
                method: 'GET',
                url: '/dashboard/week'
            }).success(function successCallback(response) {
                if (response) {
                    Object.keys(response.this_week).map(function (key) {
                        $scope.data_bank.this_week.push(response.this_week[key]);
                        $scope.data_bank.totalTWk += response.this_week[key].cdr_count;
                    });

                    if ($scope.username != 'all') {

                        $scope.filtered_data.this_week = $scope.data_bank.this_week.filter(function (value) {
                            return value.username == $scope.username;
                        });
                        $scope.filtered_data.totalTWk += $scope.filtered_data.this_week[key].cdr_count;
                    }
                    else {
                        $scope.filtered_data = $scope.data_bank;
                    }
                }
            }).error(function errorCallback(err) {
                location.href = '/logout';
            });
        }, 20);
    };

    var load_month = function () {
        $timeout(function () {
            $http({
                method: 'GET',
                url: '/dashboard'
            }).success(function successCallback(response) {
                if (response) {
                    Object.keys(response.month).map(function (key) {
                        $scope.data_bank.month.push(response.month[key]);
                        $scope.data_bank.totalMonth += response.month[key].cdr_count;
                    });

                    if ($scope.username != 'all') {

                        $scope.filtered_data.month = $scope.data_bank.month.filter(function (value) {
                            return value.username == $scope.username;
                        });
                        $scope.filtered_data.totalMonth += $scope.filtered_data.month[key].cdr_count;
                    }
                    else {
                        $scope.filtered_data = $scope.data_bank;
                    }
                }
            }).error(function errorCallback(err) {
                location.href = '/logout';
            });
        }, 20);
    };

    var startParallel = function () {
        $q.all([load_data()]).then(
            function (successResult) { // execute this if ALL promises are resolved (successful)
            }, function (failureReason) { // execute this if any promise is rejected (fails) - we don't have any reject calls in this demo
                location.href = '/logout';
            }
        );
    };

    var resetCampaigns = function () {
        $scope.campaigns = [];
        $scope.campaigns_bank.data.forEach(function (elem) {
            if (elem.username === $scope.username) {
                $scope.campaigns.push(elem)
            }
        });
    };

    var resetActiveCampaigns = function () {
        $scope.active_campaigns = [];
        $scope.active_bank.forEach(function (elem) {
            if (elem.username === $scope.username) {
                $scope.active_campaigns.push(elem)
            }
        });
    };

    $scope.init = function (data, active, username) {

        $scope.data_bank = {"today": [], "yesterday": [], "totalToday": 0, "totalYday": 0, "impressionToday": 0,
            "this_week": [], "last_week": [], "month": [], "totalLWk": 0, "totalTWk": 0, "totalMonth": 0};
        $scope.filtered_data = {"today": [], "yesterday": [], "totalToday": 0, "totalYday": 0, "impressionToday": 0,
            "totalLWk": 0, "totalTWk": 0, "totalMonth": 0};
        $scope.username = username;
        $scope.campaigns_bank = {'data': []};

        $scope.campaigns_bank.data = clone(data);
        $scope.campaigns = clone(data);

        if (username != 'all') {
            resetCampaigns();
        }

        $scope.active_bank = clone(active);
        $scope.active_campaigns = clone(active);

        startParallel();
    };

    // $scope.changeActive = function (value) {
    //
    //     $timeout(function () {
    //         $scope.username = value;
    //
    //         var copy = JSON.parse(JSON.stringify($scope.data_bank));
    //         var campaigns_copy = JSON.parse(JSON.stringify($scope.campaigns_bank));
    //
    //         if (value != 'all') {
    //             $scope.campaigns = campaigns_copy.filter(function (value) {
    //                 return value.username == $scope.username;
    //             });
    //             $scope.filtered_data.today = copy.today.filter(function (value) {
    //                 return value.username == $scope.username;
    //             });
    //             $scope.filtered_data.totalToday = sum($scope.filtered_data.today, 'cdr_count');
    //             $scope.filtered_data.impressionToday = sum($scope.filtered_data.today, 'impression_count');
    //
    //             $scope.filtered_data.yesterday = copy.yesterday.filter(function (value) {
    //                 return value.username == $scope.username;
    //             });
    //             $scope.filtered_data.totalYday = sum($scope.filtered_data.yesterday, 'cdr_count');
    //
    //             $scope.filtered_data.this_week = copy.this_week.filter(function (value) {
    //                 return value.username == $scope.username;
    //             });
    //             $scope.filtered_data.totalTWk = sum($scope.filtered_data.this_week, 'cdr_count');
    //
    //             $scope.filtered_data.last_week = copy.last_week.filter(function (value) {
    //                 return value.username == $scope.username;
    //             });
    //             $scope.filtered_data.totalLWk = sum($scope.filtered_data.last_week, 'cdr_count');
    //
    //             $scope.filtered_data.month = copy.month.filter(function (value) {
    //                 return value.username == $scope.username;
    //             });
    //             $scope.filtered_data.totalMonth = sum($scope.filtered_data.month, 'cdr_count');
    //
    //             var active_copy = $.extend([], $scope.active_bank);
    //             $scope.active_campaigns = active_copy.filter(function (val) {
    //                 return val.username == $scope.username;
    //             });
    //         }
    //         else {
    //             $scope.campaigns = campaigns_copy;
    //             $scope.filtered_data = copy;
    //             $scope.filtered_data.totalToday = sum(copy.today, 'cdr_count');
    //             $scope.filtered_data.impressionToday = sum(copy.today, 'impression_count');
    //             $scope.filtered_data.totalYday = sum(copy.yesterday, 'cdr_count');
    //             $scope.filtered_data.totalTWk = sum(copy.this_week, 'cdr_count');
    //             $scope.filtered_data.totalLWk = sum(copy.last_week, 'cdr_count');
    //             $scope.filtered_data.totalMonth = sum(copy.month, 'cdr_count');
    //             $scope.active_campaigns = JSON.parse(JSON.stringify($scope.active_bank));
    //         }
    //     }, 2);
    // };
    $scope.changeActive = function () {

        $timeout(function () {

            if ($scope.username != 'all') {
                resetCampaigns();
                resetData();
                resetActiveCampaigns();
            }
            else {
                $scope.campaigns = clone($scope.campaigns_bank.data);
                $scope.filtered_data = clone($scope.data_bank);
                $scope.filtered_data.totalToday = sum($scope.data_bank.today, 'cdr_count');
                $scope.filtered_data.impressionToday = sum($scope.data_bank.today, 'impression_count');
                $scope.filtered_data.totalYday = sum($scope.data_bank.yesterday, 'cdr_count');
                $scope.filtered_data.totalTWk = sum($scope.data_bank.this_week, 'cdr_count');
                $scope.filtered_data.totalLWk = sum($scope.data_bank.last_week, 'cdr_count');
                $scope.filtered_data.totalMonth = sum($scope.data_bank.month, 'cdr_count');
                $scope.active_campaigns = clone($scope.active_bank);
            }
        }, 2);
    };
});

app.controller("ReportsController", function ($scope, $timeout, $q, $parse) {

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
                verticalAlign: 'bottom',
                borderWidth: 0
            },
            series: data.series
        }
    }

    var startParallel = function () {
        $q.all([load_data()]).then(
            function (successResult) { // execute this if ALL promises are resolved (successful)
            }, function (failureReason) { // execute this if any promise is rejected (fails) - we don't have any reject calls in this demo
                location.href = '/logout';
            }
        );
    };

    var processData = function (data) {

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

        if (Object.keys(data.result).length > 0) {
            // call records
            Object.keys(data.result).map(function (key, index) {
                // var temp_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                // var __temp = data.result[key];
                // var temp = __temp;
                // if ($scope.camp_data.username != 'all') {
                //     temp = [];
                //     temp = __temp.filter(function (z) {
                //         return z.username == $scope.camp_data.username
                //     })
                // }
                // temp.map(function (i, j) {
                //     var pos = new Date(temp[j].created_at).getDay();
                //     var b = date_range.indexOf(pos);
                //     temp_object['data'][b] = temp[j].cdr_count;
                // });
                var temp_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                var _object = data.result[key];
                _object.map(function (i, j) {
                    if ($scope.camp_data.username == 'all' || _object[j].username == $scope.camp_data.username) {
                        var pos = new Date(_object[j].created_at).getDay();
                        var b = date_range.indexOf(pos);
                        temp_object['data'][b] = _object[j].cdr_count;
                    }
                });
                $scope.camp_data.data.push(temp_object);
            });

            var cam_data = {
                "categories": week_map,
                "text": "Call Records over a week",
                "subtitle": "All campaigns",
                "yaxis_text": "Call Record",
                "series": $scope.camp_data.data
            };
            $('#camp').highcharts(buildData(cam_data));
            $scope.$apply(function () {
                $scope.base.has_records = true
            });

            // impression records
            Object.keys(data.result).map(function (key, index) {
                var impression_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                var _object = data.result[key];
                _object.map(function (i, j) {
                    if ($scope.camp_data.username == 'all' || _object[j].username == $scope.camp_data.username) {
                        var pos = new Date(_object[j].created_at).getDay();
                        var b = date_range.indexOf(pos);
                        impression_object['data'][b] = _object[j].impression_count;
                    }
                });

                $scope.camp_data.impression_data.push(impression_object);
            });

            var impression_data = {
                "categories": week_map,
                "text": "Adverts Impressions over a week",
                "subtitle": "All campaigns",
                "yaxis_text": "Call Impressions",
                "series": $scope.camp_data.impression_data
            };
            $('#impression').highcharts(buildData(impression_data));
            $scope.$apply(function () {
                $scope.base.has_impressions = true
            });

            // subscribed
            Object.keys(data.result).map(function (key, index) {
                var subscription_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                var _object = data.result[key];
                _object.map(function (i, j) {
                    if ($scope.camp_data.username == 'all' || _object[j].username == $scope.camp_data.username) {
                        var pos = new Date(_object[j].created_at).getDay();
                        var b = date_range.indexOf(pos);
                        subscription_object['data'][b] = _object[j].subscription_count;
                    }
                });

                $scope.camp_data.subscribed_data.push(subscription_object);
            });

            var subscribed_data = {
                "categories": week_map,
                "text": "Subscription Attempts over a week",
                "subtitle": "All campaigns",
                "yaxis_text": "Subscription Attempts",
                "series": $scope.camp_data.subscribed_data
            };
            $('#subscribed').highcharts(buildData(subscribed_data));
            $scope.$apply(function () {
                $scope.base.has_subscribed = true
            });

            // confirmation
            Object.keys(data.result).map(function (key, index) {
                var confirmation_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                var _object = data.result[key];
                _object.map(function (i, j) {
                    if ($scope.camp_data.username == 'all' || _object[j].username == $scope.camp_data.username) {
                        var pos = new Date(_object[j].created_at).getDay();
                        var b = date_range.indexOf(pos);
                        confirmation_object['data'][b] = _object[j].confirmation_count;
                    }
                });
                // var temp_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                // var __temp = data.result[key];
                // var temp = __temp;
                // if ($scope.camp_data.username != 'all') {
                //     temp = [];
                //     temp = __temp.filter(function (z) {
                //         return z.username == $scope.camp_data.username
                //     })
                // }
                // temp.map(function (i, j) {
                //     var pos = new Date(temp[j].created_at).getDay();
                //     var b = date_range.indexOf(pos);
                //     temp_object['data'][b] = temp[j].cdr_count;
                // });

                $scope.camp_data.confirmation_data.push(confirmation_object);
            });

            var confirmation_data = {
                "categories": week_map,
                "text": "Subscription Confirmation over a week",
                "subtitle": "All campaigns",
                "yaxis_text": "Subscription Confirmation",
                "series": $scope.camp_data.confirmation_data
            };
            $('#confirmed').highcharts(buildData(confirmation_data));
            $scope.$apply(function () {
                $scope.base.has_confirmed = true
            });

            // already subscribed
            Object.keys(data.result).map(function (key, index) {
                var subbed_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                var _object = data.result[key];
                _object.map(function (i, j) {
                    if ($scope.camp_data.username == 'all' || _object[j].username == $scope.camp_data.username) {
                        var pos = new Date(_object[j].created_at).getDay();
                        var b = date_range.indexOf(pos);
                        subbed_object['data'][b] = _object[j].already_subbed_count;
                    }
                });
                // var temp_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                // var __temp = data.result[key];
                // var temp = __temp;
                // if ($scope.camp_data.username != 'all') {
                //     temp = [];
                //     temp = __temp.filter(function (z) {
                //         return z.username == $scope.camp_data.username
                //     })
                // }
                // temp.map(function (i, j) {
                //     var pos = new Date(temp[j].created_at).getDay();
                //     var b = date_range.indexOf(pos);
                //     temp_object['data'][b] = temp[j].cdr_count;
                // });

                $scope.camp_data.subbed_data.push(subbed_object);
            });

            var subbed_data = {
                "categories": week_map,
                "text": "Already Subscribed over a week",
                "subtitle": "All campaigns",
                "yaxis_text": "Already Subscribed",
                "series": $scope.camp_data.subbed_data
            };
            $('#subbed').highcharts(buildData(subbed_data));
            $scope.$apply(function () {
                $scope.base.has_already_subscribed = true
            });

            // insufficient balance
            Object.keys(data.result).map(function (key, index) {
                var insufficient_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                var _object = data.result[key];
                _object.map(function (i, j) {
                    if ($scope.camp_data.username == 'all' || _object[j].username == $scope.camp_data.username) {
                        var pos = new Date(_object[j].created_at).getDay();
                        var b = date_range.indexOf(pos);
                        insufficient_object['data'][b] = _object[j].insufficient_count;
                    }
                });
                // var temp_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                // var __temp = data.result[key];
                // var temp = __temp;
                // if ($scope.camp_data.username != 'all') {
                //     temp = [];
                //     temp = __temp.filter(function (z) {
                //         return z.username == $scope.camp_data.username
                //     })
                // }
                // temp.map(function (i, j) {
                //     var pos = new Date(temp[j].created_at).getDay();
                //     var b = date_range.indexOf(pos);
                //     temp_object['data'][b] = temp[j].cdr_count;
                // });

                $scope.camp_data.insufficient_data.push(insufficient_object);
            });

            var insufficient_data = {
                "categories": week_map,
                "text": "Insufficent Balance over a week",
                "subtitle": "All campaigns",
                "yaxis_text": "Insufficent Balance",
                "series": $scope.camp_data.insufficient_data
            };
            $('#insufficient').highcharts(buildData(insufficient_data));
            $scope.$apply(function () {
                $scope.base.has_insufficient = true
            });

            // success
            Object.keys(data.result).map(function (key, index) {
                var success_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                var _object = data.result[key];
                _object.map(function (i, j) {
                    if ($scope.camp_data.username == 'all' || _object[j].username == $scope.camp_data.username) {
                        var pos = new Date(_object[j].created_at).getDay();
                        var b = date_range.indexOf(pos);
                        success_object['data'][b] = _object[j].success_count;
                    }
                });
                $scope.camp_data.success_data.push(success_object);
            });

            var success_data = {
                "categories": week_map,
                "text": "Successful Subscriptions over a week",
                "subtitle": "All campaigns",
                "yaxis_text": "Successful Subscriptions",
                "series": $scope.camp_data.success_data
            };
            $('#success').highcharts(buildData(success_data));
            $scope.$apply(function () {
                $scope.base.has_success = true
            });

            // failure
            Object.keys(data.result).map(function (key, index) {
                var failed_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                var _object = data.result[key];
                _object.map(function (i, j) {
                    if ($scope.camp_data.username == 'all' || _object[j].username == $scope.camp_data.username) {
                        var pos = new Date(_object[j].created_at).getDay();
                        var b = date_range.indexOf(pos);
                        failed_object['data'][b] = _object[j].failed_count;
                    }
                });
                // var failed_object = {"name": data.result[key][0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
                // var failed = data.result[key];
                // var temp_failed = failed;
                // if ($scope.camp_data.username != 'all') {
                //     temp_failed = [];
                //     temp_failed = failed.filter(function (z) {
                //         return z.username == $scope.camp_data.username
                //     })
                // }
                // temp_failed.map(function (i, j) {
                //     var pos = new Date(temp[j].created_at).getDay();
                //     var b = date_range.indexOf(pos);
                //     failed_object['data'][b] = temp_failed[j].cdr_count;
                // });

                $scope.camp_data.failed_data.push(failed_object);
            });

            var failed_data = {
                "categories": week_map,
                "text": "Failed Subscriptions over a week",
                "subtitle": "All campaigns",
                "yaxis_text": "Failed Subscriptions",
                "series": $scope.camp_data.failed_data
            };
            $('#failed').highcharts(buildData(failed_data));
            $scope.$apply(function () {
                $scope.base.has_failed = true
            });
        }
        else {

        }
    };

    var load_data = function () {
        $timeout(function () {
            $.get("/campaign/period", function (_data, status) {
                $scope.base.data = JSON.parse(_data);
                processData($scope.base.data);
            });
        }, 5)
    };

    $scope.init = function () {
        $scope.camp_data = {"username": 'all', "data": [], "impression_data": [], "subscribed_data": [], "confirmation_data": [], "subbed_data": [],  "insufficient_data": [], "success_data": [], "failed_data": []};
        $scope.base = {data: [], has_records: false, has_impressions: false, has_already_subscribed: false, has_success: false, has_failed: false, has_subscribed: false, has_confirmed: false, has_insufficient: false};
        startParallel();
    };

    $scope.changeActive = function () {
        $timeout(function () {
            var username = $scope.camp_data.username;
            $scope.camp_data = {"username": username, "data": [], "impression_data": [], "subscribed_data": [], "confirmation_data": [], "subbed_data": [],  "insufficient_data": [], "success_data": [], "failed_data": []};
            $.get("/campaign/period", function (_data, status) {
                $scope.base.data = JSON.parse(_data);
                processData($scope.base.data);
            });
        }, 5)
    };

    $scope.filterReport = function (start, end) {
        var start_date = new Date(start);
        var end_date = new Date(end);
        $.post('/record/filter', {
            start: start_date,
            end: end_date
        }, function (data, status) {
            if (status == 'success') {
                $scope.camp_data = {"username": 'all', "data": [], "impression_data": [], "subscribed_data": [], "confirmation_data": [], "subbed_data": [],  "insufficient_data": [], "success_data": [], "failed_data": []};
                $scope.base = {data: [], has_records: false, has_impressions: false, has_already_subscribed: false, has_success: false, has_failed: false, has_subscribed: false, has_confirmed: false, has_insufficient: false};
                $timeout(function () {
                    $scope.base.data = JSON.parse(data);
                    processData($scope.base.data);
                }, 5);
            }
        })
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
                verticalAlign: 'bottom',
                borderWidth: 0
            },
            series: data.series
        }
    }

    $scope.init = function (campaign_id) {

        var camp_data = {"data": [], "impression_data": [], "subscribed_data": [], "confirmation_data": [], "subbed_data": [],  "insufficient_data": [], "success_data": [], "failed_data": []};

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

            var cdr_object = {"name": data.result[0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
            var impression_object = {"name": data.result[0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
            var subscribed_object = {"name": data.result[0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
            var confirmation_object = {"name": data.result[0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
            var subbed_object = {"name": data.result[0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
            var insufficient_object = {"name": data.result[0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
            var success_object = {"name": data.result[0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};
            var failed_object = {"name": data.result[0].campaign_name, "data": [0, 0, 0, 0, 0, 0, 0]};

            var temp = data.result;
            temp.map(function (i, j) {
                var pos = new Date(temp[j].created_at).getDay();
                var b = date_range.indexOf(pos);
                cdr_object['data'][b] = temp[j].cdr_count;
                impression_object['data'][b] = temp[j].impression_count;
                subscribed_object['data'][b] = temp[j].subscription_count;
                confirmation_object['data'][b] = temp[j].confirmation_count;
                subbed_object['data'][b] = temp[j].already_subbed_count;
                insufficient_object['data'][b] = temp[j].insufficient_count;
                success_object['data'][b] = temp[j].success_count;
                failed_object['data'][b] = temp[j].failed_count;
            });
            camp_data.data.push(cdr_object);
            camp_data.impression_data.push(impression_object);
            camp_data.subscribed_data.push(subscribed_object);
            camp_data.confirmation_data.push(confirmation_object);
            camp_data.subbed_data.push(subbed_object);
            camp_data.insufficient_data.push(insufficient_object);
            camp_data.success_data.push(success_object);
            camp_data.failed_data.push(failed_object);

            // call records
            var cam_data = {
                "categories": week_map,
                "text": "Call Records over a week",
                "subtitle": data.result[0].campaign_name,
                "yaxis_text": "Call Record",
                "series": camp_data.data
            };
            $('#camp').highcharts(buildData(cam_data));

            // call impressions
            var impression_data = {
                "categories": week_map,
                "text": "Impressions over a week",
                "subtitle": data.result[0].campaign_name,
                "yaxis_text": "Call Impressions",
                "series": camp_data.impression_data
            };
            $('#impression').highcharts(buildData(impression_data));

            // subscription attempts
            var subscribed_data = {
                "categories": week_map,
                "text": "Subscription Attempts over a week",
                "subtitle": data.result[0].campaign_name,
                "yaxis_text": "Subscription Attempts",
                "series": camp_data.subscribed_data
            };
            $('#subscribed').highcharts(buildData(subscribed_data));

            // confirmation
            var confirmation_data = {
                "categories": week_map,
                "text": "Subscription Confirmation over a week",
                "subtitle": data.result[0].campaign_name,
                "yaxis_text": "Subscription Confirmation",
                "series": camp_data.confirmation_data
            };
            $('#confirmation').highcharts(buildData(confirmation_data));

            // already subscribed
            var subbed_data = {
                "categories": week_map,
                "text": "Already Subscribed over a week",
                "subtitle": data.result[0].campaign_name,
                "yaxis_text": "Already Subscribed",
                "series": camp_data.subbed_data
            };
            $('#subbed').highcharts(buildData(subbed_data));

            // subscription attempts
            var insufficient_data = {
                "categories": week_map,
                "text": "Insufficient Balance over a week",
                "subtitle": data.result[0].campaign_name,
                "yaxis_text": "Insufficient Balance",
                "series": camp_data.insufficient_data
            };
            $('#insufficient').highcharts(buildData(insufficient_data));

            // success
            var success_data = {
                "categories": week_map,
                "text": "Successful Subscriptions over a week",
                "subtitle": data.result[0].campaign_name,
                "yaxis_text": "Successful Subscriptions",
                "series": camp_data.success_data
            };
            $('#success').highcharts(buildData(success_data));

            // subscription attempts
            var failed_data = {
                "categories": week_map,
                "text": "Failed Subscriptions over a week",
                "subtitle": data.result[0].campaign_name,
                "yaxis_text": "Failed Subscriptions",
                "series": camp_data.failed_data
            };
            $('#failed').highcharts(buildData(failed_data));
        });
    };
});
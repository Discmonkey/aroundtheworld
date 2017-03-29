/**
 * Created by mgrinchenko on 3/27/2017.
 * The angular app is housed here. Due to time constraints, and because the focus of this project was creating an
 * effective back end api for querying flight data, This app is intentionally simple. Everything is largely attached
 * to the scope variables, and there no hidden methods. Furthermore in lieu of any routing system, navigation among the 
 * 3 pages is controlled via simple ng-show directives. 
 */
var app = angular.module('travel', ['ui.bootstrap', 'chart.js']);
Chart.defaults.global.showTooltips = false;
app.directive('airportPicker', ['$http', function($http) {
    return {
        scope: {
            modelValue: '=ngModel'
        },
        restrict: 'E',
        replace: true,
        template: '<div class="controls"><input type="text" ng-model="modelValue" placeholder="Search for Airport by City or Country"  uib-typeahead="airport as airport.name for airport in searchAirports($viewValue) | limitTo:10" typeahead-loading="loadingAirports" typeahead-no-results="noResults" typeahead-min-length="2" typeahead-wait-ms="250" typeahead-editable = "false" typeahead-select-on-exact ="true" class="form-control"><i ng-show="loadingAirports" class="glyphicon glyphicon-refresh"></i><div ng-show="noResults"><i class="glyphicon glyphicon-remove"></i> No Results Found</div></div>',
        link: function(scope,elem,attrs){
            scope.searchAirports = function(val) {
                return $http.get('get/searchAirports.php', {
                    params: {
                        search_term: val
                    }
                }).then(function(response){
                    return response.data;
                });
            };
        }
    };
}]);

app.directive('datePicker',function(){
    return {
        scope:{
            modelValue: '=ngModel',
            options: '=ngCalOptions',
            format: '@ngCalFormat'
        },
        restrict: 'A',
        replace: true,
        template: '<div class="controls">' +
        '<button type="button" class="btn btn-default" ng-click="opened()" style="margin-bottom:5px;">' +
        '<i class="fa fa-calendar" style="display:inline;"></i>' +
        '</button>' +
        '<input style="width:50%;display:inline" type="text" class="form-control dateSelection" uib-datepicker-popup={{format}} ng-model="modelValue" is-open="open" datepicker-options="options"/></div>',
        link: function (scope, elem, attrs) {
            scope.open = false;
            scope.opened= function () {
                scope.open=true;
            };
        }
    };
});

app.service('chart', function() {

});

app.controller('main', function($scope, $http){
    $scope.origin = '';
    $scope.currentDestination = '';
    $scope.destinations = [];
    $scope.showOrigin = true;
    $scope.showDestinations = false;
    $scope.depDate = '';
    $scope.returnDate = '';
    $scope.showResults = false;
    $scope.today = new Date();
    $scope.results = [];
    $scope.tableSort = 'IATA';
    $scope.submitDeparture = function() {
        if ( $scope.origin ) {
            $scope.showDestinations = true;
            $scope.showOrigin = false;
        }
    };

    $scope.chartTitles = {
        'distance': 'Distance from Origin (miles)',
        'cost_per_mile': 'Cost per Mile ($/m)',
        'cost': 'Cost ($)',
        'avg_cost': 'Average Cost ($)',
        'duration': 'Flight Duration (min)'
    };

    $scope.chartTitle = '';

    $scope.barChart = {
        'data': [],
        'series': ['Flights'],
        'labels': []
    };

    $scope.submitDestination = function () {
        if ($scope.destinations.length == 0 ||
            $scope.destinations[$scope.destinations.length - 1].length == 4) {
            $scope.destinations.push([$scope.currentDestination]);
        } else {
            $scope.destinations[$scope.destinations.length-1].push($scope.currentDestination);
        }
        
        $scope.currentDestination = '';
    };

    $scope.individualRequest = function(num) {
        $http.post('/getTrip.php', {
            num: num
        }).then(function(res) {
            if ( res.data ) {
                $scope.results[num]['duration'] = res.data['duration'];
                $scope.results[num]['cost'] = res.data['cost'];
                $scope.results[num]['avg_cost'] = res.data['avg_cost'];
                $scope.results[num]['avg_duration'] = res.data['avg_duration'];
                $scope.results[num]['cost_per_mile'] = $scope.results[num]['cost'] / $scope.results[num]['distance'];
            } else {
                delete $scope.results[num];
            }
        });
    };
    $scope.submitAll = function () {
        var flat_destinations = [].concat.apply([], $scope.destinations);
        if ($scope.origin != '' &&
            flat_destinations.length > 0 &&
            $scope.depDate != '' && $scope.depDate != undefined &&
            $scope.returnDate != '' && $scope.returnDate != undefined) {

            $http.post('/submitAll.php', {
                origin: $scope.origin,
                destinations: flat_destinations,
                depDate: $scope.depDate,
                returnDate: $scope.returnDate
            }).then(function(res) {
                $scope.results = res.data;
                var length = res.data.length;
                $scope.showResults = true;
                for ( var i = 0; i < length; i++) {
                    $scope.individualRequest(i);
                }

                $scope.setBarChartData('distance');
            });
        }
    };

    $scope.deleteAirport = function(row, col) {
        
        if ($scope.destinations[0].length > 1 ) {
            var num_rows = $scope.destinations.length;
            var num_cols = $scope.destinations[num_rows - 1].length;
            
            if ( num_rows - 1 == row && num_cols - 1 == col) {
                $scope.destinations[row].splice(col,1);
                return;
            }
            var last_element = $scope.destinations[num_rows - 1 ][num_cols - 1];
            if ( num_cols == 1 ) {
                $scope.destinations.splice(num_rows-1,1);
            } else {
                $scope.destinations[row].splice(col, 1);
            }
            $scope.destinations[row][col] = last_element;
            
        } else {
            $scope.destinations.splice(0, 1);
        }

    };
    
    $scope.backToOrigin = function () {
        $scope.showDestinations = false;
        $scope.showOrigin = true;
    };
    
    $scope.backToDestinations = function () {
        $scope.flip = false;
        $scope.showResults = false;
    };

    $scope.setBarChartData = function(key) {
        // this function allows users to sort table columns, and also sets the chart in the results
        //
        if ( Object.keys($scope.chartTitles).indexOf(key) > -1 ) {
            var temp_labels = [];
            var temp_values = [];
            for (var i in $scope.results) {
                temp_labels.push($scope.results[i].IATA);
                temp_values.push($scope.results[i][key]);
            }
            $scope.chartTitle = $scope.chartTitles[key];
            $scope.barChart.data[0] = temp_values;
            $scope.barChart.labels = temp_labels;
        }

        $scope.tableSort = key == $scope.tableSort ? '-'+key : key;
    };
    
});
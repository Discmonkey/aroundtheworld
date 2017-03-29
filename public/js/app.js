/**
 * Created by mgrinchenko on 3/27/2017.
 */
var app = angular.module('travel', ['ui.bootstrap']);

app.directive('airportPicker', ['$http', function($http) {
    return {
        scope: {
            modelValue: '=ngModel'
        },
        restrict: 'E',
        replace: true,
        template: '<div class="controls"><input type="text" ng-model="modelValue" placeholder="Search for Airport by City"  uib-typeahead="airport as airport.name for airport in searchAirports($viewValue) | limitTo:10" typeahead-loading="loadingAirports" typeahead-no-results="noResults" typeahead-min-length="2" typeahead-wait-ms="250" typeahead-editable = "false" typeahead-select-on-exact ="true" class="form-control"><i ng-show="loadingAirports" class="glyphicon glyphicon-refresh"></i><div ng-show="noResults"><i class="glyphicon glyphicon-remove"></i> No Results Found</div></div>',
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

app.controller('main', function($scope, $http){
    $scope.origin = '';
    $scope.currentDestination = '';
    $scope.destinationsDisplay = [];
    $scope.destinationsSubmit = [];
    $scope.showOrigin = true;
    $scope.showDestinations = false;
    $scope.startDate = '';
    $scope.endDate = '';
    $scope.submitDeparture = function() {
        if ( $scope.origin ) {
            $scope.showDestinations = true;
            $scope.showOrigin = false;
        }
    };

    $scope.submitDestination = function () {
        if ($scope.destinationsDisplay.length == 0 ||
            $scope.destinationsDisplay[$scope.destinationsDisplay.length - 1].length == 4) {
            $scope.destinationsDisplay.push([$scope.currentDestination.IATA]);
        } else {
            $scope.destinationsDisplay[$scope.destinationsDisplay.length-1].push($scope.currentDestination.IATA);
        }
        $scope.destinationsSubmit.push($scope.currentDestination);
        $scope.currentDestination = '';
    };

    $scope.submitAll = function () {
        if ($scope.origin != '' &&
            $scope.destinationsSubmit.length > 0 &&
            $scope.startDate != '' && $scope.startDate != undefined &&
            $scope.endDate != '' && $scope.endDate != undefined) {

            $http.post('/post/submitAll.php', {
                origin: $scope.origin,
                destinations: $scope.destinationsSubmit,
                startDate: $scope.startDate,
                endDate: $scope.endDate
            }).then(function(data) {
                console.log(data);
            });
        }
    }
});
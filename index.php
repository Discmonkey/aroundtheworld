<?php
/**
 * Author: maxg
 * Date: 3/25/17
 * Time: 3:23 PM
 * This project aims to give users the ability to easily compare flight prices and other metrics across multiple locations
 * it also aims to build a database of cheap ticket price, for future, easy reference. The front end is is created in angular,
 * and uses simple features to allow the user to:
 * 1) Choose an origin ( airport )
 * 2) Choose possible destinations (airports) -- there are no limits on the number, however in the future I will probably
 * cap the destinations at around 20.
 * 3) Choose arrival and return dates -- I believe more flexibility. However, due to the limiting nature of API calls I 
 * may have to wait until I build up a larger database of cached trips, or create a token manager.
 * 4) Display results -- Results come in one at a time. Users can also sort by any of the columns, and display the metrics in 
 * a barchart graph.
 */


include "pieces/header.php";

?>

<body>
    <div class="container" ng-app="travel">
        <div ng-controller="main">
            <div id="transform">
                <div id="front" ng-show="!showResults">
                    <div class="content-container">
                        <div class="content text-center" id="main-description" ng-show="showOrigin">
                            <p>
                                Sometimes you don't know where you want to go. 
                            </p>
                            
                            <p>
                                Taiwan? Chile? Belgium? Togo?
                            </p>
                            
                            <p>
                                There are more places to visit in this world than we have days on Earth. 
                            </p>
                            <p>
                                This site aims to give you all the information you need to start your odyssey, wherever that may be.
                                Choose from any airport in the world, over any date range, and compare your destinations.
                            </p>
                            
                            <p>
                                Beware! Prices change daily, and finding the best tickets may mean coming back often.
                            </p>
                        </div>
            
                        <div class="content" ng-show="showOrigin">
                            <airport-picker ng-model="origin"></airport-picker>
                            <button class="btn btn-success btn-block" ng-click="submitDeparture()"> Choose Departure Point</button>
                        </div>
            
                        <div class="content" ng-show="showDestinations">
                            <button class="btn btn-warning" ng-click="backToOrigin()"> <i class="fa fa-chevron-left"></i> Go Back </button>
                            <h3 class="bold"> Choose Destinations </h3>
                            <airport-picker ng-model="currentDestination"></airport-picker>
                            <button class="btn btn-primary btn-block" ng-click="submitDestination()"> Submit Destination
                                <i class="fa fa-chevron-down"></i></button>
                            <table class="table table-bordered table-responsive table-striped bold padding-top">
                                <tr ng-repeat="(rowIndex, row) in destinations track by $index">
                                    <td class="padding-top" ng-repeat="(colIndex, col) in row track by $index">
                                        {{col.IATA}}
                                        <button class="btn btn-sm btn-danger delete"
                                                             ng-click="deleteAirport(rowIndex, colIndex)">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </table>
                            <div id="dates">
                                <h3 class="bold padding-top"> Choose Dates </h3>
                                <div class="row padding-top">
                                    <div class="col-md-6 bold">
                                        Estimated Departure Date
                                    </div>
                                    <div class="col-md-6 bold">
                                        Estimated Return Date
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6"><input type="text" date-picker=""
                                                                 ng-cal-options="{showWeeks: false, maxDate: returnDate, minDate:today }"
                                                                 ng-cal-format="{{'MM/dd/yyyy'}}" ng-model="depDate"></div>
                                    <div class="col-md-6"><input type="text" date-picker=""
                                                                 ng-cal-options="{showWeeks: false, minDate: depDate }"
                                                                 ng-cal-format="{{'MM/dd/yyyy'}}" ng-model="returnDate"></div>
                                </div>
                            </div>
                            <button class="btn btn-success btn-block" ng-click="submitAll()"> Generate Plans</button>
                        </div>
                    </div>
                </div>
                <div ng-show="showResults">
                    <div class="content-container">
                        <div class="content">
                            <button class="btn btn-warning" ng-click="backToDestinations()"> <i class="fa fa-chevron-left"></i> Go Back </button>
                            <table class="bold table table-bordered table-striped margin-top">
                                <tr>
                                    <th ng-click="setBarChartData('IATA')">Code
                                        <i ng-class="{'fa fa-sort-asc': tableSort === 'IATA',
                                        'fa fa-sort-desc': tableSort === '-IATA',
                                        'fa fa-sort': true}"></i></th>
                                    <th ng-click="setBarChartData('city')">City <i ng-class="{'fa fa-sort-asc': tableSort === 'city',
                                        'fa fa-sort-desc': tableSort === '-city',
                                        'fa fa-sort': true}"></i></th>
                                    <th ng-click="setBarChartData('country')">Country <i ng-class="{'fa fa-sort-asc': tableSort === 'country',
                                        'fa fa-sort-desc': tableSort === '-country',
                                        'fa fa-sort': true}"></i></th>
                                    <th ng-click="setBarChartData('distance')">Distance <i ng-class="{'fa fa-sort-asc': tableSort === 'distance',
                                        'fa fa-sort-desc': tableSort === '-distance',
                                        'fa fa-sort': true}"></i></th>
                                    <th ng-click="setBarChartData('duration')">Duration <i ng-class="{'fa fa-sort-asc': tableSort === 'duration',
                                        'fa fa-sort-desc': tableSort === '-duration',
                                        'fa fa-sort': true}"></i></th>
                                    <th ng-click="setBarChartData('cost')">Cost <i ng-class="{'fa fa-sort-asc': tableSort === 'cost',
                                        'fa fa-sort-desc': tableSort === '-cost',
                                        'fa fa-sort': true}"></i></th>
                                    <th ng-click="setBarChartData('avg_cost')">Avg Cost (hist) <i ng-class="{'fa fa-sort-asc': tableSort === 'avg_cost',
                                        'fa fa-sort-desc': tableSort === '-avg_cost',
                                        'fa fa-sort': true}"></i></th>
                                    <th ng-click="setBarChartData('cost_per_mile')">Cost/Mile <i ng-class="{'fa fa-sort-asc': tableSort === 'cost_per_mile',
                                        'fa fa-sort-desc': tableSort === '-cost_per_mile',
                                        'fa fa-sort': true}"></i></th>
                                </tr>
                                <tr ng-repeat="row in results | orderBy : tableSort" ng-class="{'no-flights': row['cost'] == 0}">
                                    <td>{{row['IATA']}}</td>
                                    <td>{{row['city']}}</td>
                                    <td>{{row['country']}}</td>
                                    <td>{{row['distance'] | number:0}}</td>
                                    <td>{{row['duration'] | number}}</td>
                                    <td>{{row['cost'] | currency}}</td>
                                    <td>{{row['avg_cost'] | currency}}</td>
                                    <td>{{row['cost_per_mile'] | currency}}</td>
                                </tr>
                            </table>
                            <label> {{chartTitle}}</label>
                            <canvas id="bar" chart-data="barChart.data"
                                    chart-labels="barChart.labels"
                                    chart-series="barChart.series" class="chart chart-bar">
                            </canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
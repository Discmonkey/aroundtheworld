<?php
/**
 * Created by PhpStorm.
 * User: maxg
 * Date: 3/25/17
 * Time: 3:23 PM
 */

include "pieces/header.php";

?>

<body>
    <div class="container" ng-app="travel">
        <div ng-controller="main">
            <div id="transform" ng-class="{'flipped': flip}">
                <div id="front">
                    <div id="content-container">
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
                            <button class="btn btn-warning"> <i class="fa fa-chevron-left"></i> Go Back </button>
                            <h3 class="bold"> Choose Destinations </h3>
                            <airport-picker ng-model="currentDestination"></airport-picker>
                            <button class="btn btn-primary btn-block" ng-click="submitDestination()"> Submit Destination
                                <i class="fa fa-chevron-down"></i></button>
                            <table class="table table-bordered table-responsive table-striped bold">
                                <tr ng-repeat="row in destinationsDisplay">
                                    <td ng-repeat="col in row">{{col}}</td>
                                </tr>
                            </table>
                            <div id="dates">
                                <h3 class="bold"> Choose Dates </h3>
                                <div class="row">
                                    <div class="col-md-6 bold">
                                        Estimated Departure Date
                                    </div>
                                    <div class="col-md-6 bold">
                                        Estimated Return Date
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6"><input type="text" date-picker=""
                                                                 ng-cal-options="{showWeeks: false, maxDate: returnDate }"
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
                <div id="back">
                    <div class="content-container">
                        <table class="bold table table-bordered table-striped">
                            <tr>
                                <th>Code</th>
                                <th>City</th>
                                <th>Country</th>
                                <th>Distance</th>
                                <th>Duration</th>
                                <th>Cost</th>
                                <th>Avg Cost (hist)</th>
                                <th>Avg Duration (hist)</th>
                                <th>Cost/Mile</th>
                            </tr>
                            <tr ng-repeat="row in results">
                                <td>row['IATA']</td>
                                <td>row['city']</td>
                                <td>row['country']</td>
                                <td>row['distance']</td>
                                <td>row['duration']</td>
                                <td>row['cost']</td>
                                <td>row['avg_cost']</td>
                                <td>row['avg_duration']</td>
                                <td>row['cost_per_mile']</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
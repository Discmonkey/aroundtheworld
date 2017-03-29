<?php
/**
 * Created by PhpStorm.
 * User: maxg
 * Date: 3/25/17
 * Time: 3:23 PM
 */

# classes
include 'includes/DBConnector.php';

# initialize classes
$connection = new DBConnector();
//$airportMdl = new AirportMdl($connection);
//$airportCtrl = new AirportCtrl($airportMdl);

# handle user
session_start();
$user_id = $_SESSION['user_id'] ?? 0;

include "pieces/header.php";

?>

<body>
    <div class="container" ng-app="travel">
        <div id="content-container" ng-controller="main">
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
                <table class="table table-bordered table-responsive table-striped">
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
                                                     ng-cal-options="{showWeeks: false, maxDate: endDate }"
                                                     ng-cal-format="{{'MM/dd/yyyy'}}" ng-model="startDate"></div>
                        <div class="col-md-6"><input type="text" date-picker=""
                                                     ng-cal-options="{showWeeks: false, minDate: startDate }"
                                                     ng-cal-format="{{'MM/dd/yyyy'}}" ng-model="endDate"></div>
                    </div>
                </div>
                <button class="btn btn-success btn-block" ng-click="submitAll()"> Generate Plans</button>
            </div>
        </div>
    </div>
</body>
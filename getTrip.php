<?php
/**
 * Created by PhpStorm.
 * User: mgrinchenko
 * Date: 3/28/2017
 * Time: 9:29 PM
 */
# This endpoint is responsible for querying the API. By isolating this operation I believe we achieve a cleaner code
# base and a better user experience. If there were a way to send a bulk request to the google QPX api 
# we would make this endpoint capable of handling bulk requests

include "includes/DBConnector.php";
include "includes/airport.php";
include "includes/trip.php";
include "includes/Requests.php";
include "includes/apis/QPX.php";

Requests::register_autoloader();
$connection = new DBConnector();
$airPort = new Airport();

session_start();

$origin = $_SESSION['origin'] ?? null;
$dep_date = $_SESSION['dep_date'] ?? null;
$return_date = $_SESSION['return_date'] ?? null;
$destinations = $_SESSION['destinations'] ?? null;

if ($origin == null || $dep_date == null || $return_date == null || $destinations == null) {
    return;
}

$raw_data = file_get_contents('php://input');
$assoc_arr = json_decode($raw_data, true);

$num = $assoc_arr['num'];

$trip = new Trip($origin->IATA, $destinations[$num]->IATA, $dep_date, $return_date);

# Check the database to see if the same flight was queried by someone else recently. 
if (!$trip->check_cache($connection)) {
    $qpx = new QPX();
    $success = $qpx->get_plan($trip);
    
    if ($success) {
        $trip->cache($connection);
    }
}

# get the average price for this flight ( origin -> destination and back ) for all dates. 
$trip->get_average($connection);

echo json_encode($trip->to_array());


















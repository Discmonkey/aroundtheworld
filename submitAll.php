<?php
/**
 * Created by PhpStorm.
 * User: mgrinchenko
 * Date: 3/28/2017
 * Time: 8:02 PM
 */

# In order to facilitate the user experience we will keep a queue of API requests in the session,
# this page is responsible for creating that queue and sending the initial data back to the client.
# The initial data will simply be a list of cities and the distance between them and the departure point.




session_start();
$connection = new DBConnector();

$raw_data = file_get_contents('php://input');
$assoc_arr = json_decode($raw_data, true);

$dep_date = (new DateTime($assoc_arr['depDate']))->format('Y-m-d');
$return_date = (new DateTime($assoc_arr['returnDate']))->format('Y-m-d');

$destinations = $assoc_arr['destinations'];
$destination_ids = array_map(function($item) { return $item['id'];}, $destinations);
$destination_ids []= $assoc_arr['origin']['id'];

$airports = Airport::bulk_load_from_ids($destination_ids, $connection);

$origin = array_pop($airports);

$return_array = [];
foreach( $airports as $airport ) {
    $airport->set_distance($origin);
    $temp_arr = $airport->to_array();
    $temp_arr['duration'] = 0;
    $temp_arr['cost'] = 0;
    $temp_arr['avg_cost'] = 0;
    $temp_arr['ang_duration'] = 0;
    $temp_arr['cost_per_mile'] = 0;
    
    #add additional fields needed by front end
    $return_array []= $temp_arr;

}

// put objects into session to be used by getTrip.php
$_SESSION['origin'] = $origin;
$_SESSION['dep_date'] = $dep_date;
$_SESSION['return_date'] = $return_date;
$_SESSION['destinations'] = $airports;

echo json_encode($return_array);










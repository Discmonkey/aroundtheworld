<?php
/**
 * Created by PhpStorm.
 * User: mgrinchenko
 * Date: 3/28/2017
 * Time: 9:29 PM
 */
# This endpoint is responsible for querying the API, by isolating this operation I believe we achieve a cleaner code
# base, if there were a way to send a bulk request to the google QPX api -- as i had originally thought there was --
# we would make this endpoint capable of handling bulk requests

include "includes/DBConnector.php";
include "includes/airport.php";
include "includes/trip.php";
include "includes/Requests.php";
include "includes/apis/QPX.php";
Requests::register_autoloader();

$airPort = new Airport();

session_start();

$origin = $_SESSION['origin'] ?? null;
$dep_date = $_SESSION['dep_date'] ?? null;
$return_date = $_SESSION['return_date'] ?? null;
$destination = $_SESSION['destinations'] ?? null;

$raw_data = file_get_contents('php://input');
$assoc_arr = json_decode($raw_data, true);

$num = $assoc_arr['num'];











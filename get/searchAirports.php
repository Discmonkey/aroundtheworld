<?php
/**
 * Created by PhpStorm.
 * User: mgrinchenko
 * Date: 3/27/2017
 * Time: 9:42 PM
 * This endpoint was created to allow the user to 'live search' through the database of airports I have hosted on 
 * Amazon. I hope to reduce overall network traffic by sending the user 10 or less specific airports instead of 
 * all 7000 at once. 
 */
include "../includes/DBConnector.php";

$connection = new DBConnector();
$search_term = $_GET['search_term'] ?? '';

if ($search_term == '') {
    return;
} else {
    $search_term = '%'.$search_term.'%';
}

$results = $connection->query("SELECT concat(name, ', ', country, ' (', IATA, ')') as name, id, IATA
FROM airports WHERE city like ? or country like ? and IATA != '' limit 10", [$search_term, $search_term]);

echo json_encode($results);





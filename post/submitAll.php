<?php
/**
 * Created by PhpStorm.
 * User: mgrinchenko
 * Date: 3/28/2017
 * Time: 8:02 PM
 */
include "../includes/DBConnector.php";

$connection = new DBConnector();
session_start();

$raw_data = file_get_contents('php://input');
$assoc_arr = json_decode($raw_data, true);


echo($assoc_arr);

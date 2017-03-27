<?php
/**
 * Created by PhpStorm.
 * User: maxg
 * Date: 3/25/17
 * Time: 3:23 PM
 */
include 'includes/DBConnector.php';

$connection = new DBConnector();



$num_results = $connection->insert("INSERT INTO users (userName) VALUES ('angel')", []);
$result = $connection->query("SELECT * FROM users WHERE id > ?", [4]);
var_dump($num_results);
var_dump($result);

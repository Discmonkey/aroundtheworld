<?php

/**
 * Created by PhpStorm.
 * User: mgrinchenko
 * Date: 3/28/2017
 * Time: 11:05 PM
 */

# holds all information relevant to a single trip, in other words it has the origin, destination, and departure and arrival dates.
# it also responsible for caching itself after every successful query, finding the average values of past trips, and all
# other management of trips associated with this project
class Trip
{
    public $origin = '';
    public $destination = '';
    public $dep_date = '';
    public $arr_date = '';
    public $duration = 0;
    public $cost = 0;
    public $avg_cost = 0;
    public $avg_duration = 0;
    public $cost_per_mile = 0;
    
    public function cache($connection) {
        $query = "INSERT INTO trips (origin, destination, departureDate, arrivalDate, duration, cost, timePulled)
                  VALUES (?,?,?,?,?,?, CURDATE())";
        $num = $connection->insert($query,[$this->origin, $this->destination, $this->dep_date, $this->arr_date,
                            $this->duration, $this->cost]);
        return $num;
    }

    public function set_average($connection) {
        $query = "SELECT avg(duration) duration, avg(cost) cost FROM TRIPS where origin=? and destination=?";
        $result = $connection->query($query, [$this->origin, $this->destination]);

        $this->avg_cost = $result['cost'];
        $this->avg_duration = $result['duration'];
        
        return 0;
    }
}
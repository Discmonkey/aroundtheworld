<?php

/**
 * Created by PhpStorm.
 * User: mgrinchenko
 * Date: 3/28/2017
 * Time: 11:05 PM
 */

# holds all information relevant to a single trip, in other words it has the origin, destination, and departure and arrival dates.
# It is also responsible for caching itself after every successful query, finding the average values of past trips, and all
# other management of trips associated with this project.
class Trip
{
    public $origin = '';
    public $destination = '';
    public $dep_date = '';
    public $return_date = '';
    public $duration = 0;
    public $cost = 0;
    public $avg_cost = 0;
    public $avg_duration = 0;
    public $cost_per_mile = 0;
    public $success = false;
    
    public function __construct($origin, $destination, $dep_date, $return_date) {
        $this->origin = $origin;
        $this->destination = $destination;
        $this->dep_date = $dep_date;
        $this->return_date = $return_date;
        
    }

    public function check_cache($connection) {
        # avoid querying the api if there is a trip for the exact destination, departure and dates that was queried less than 3 hours ago
        # The three hour mark is a fairly arbitrarily measure. As more flight data is collected, I hope to gain a better understanding
        # of how often ticket prices change. 
        $query = "SELECT cost, duration FROM trips WHERE origin = ? and destination = ? and departureDate = ? and returnDate = ? 
                  and timePulled > date_sub(NOW(), interval 3 hour)";

        $rows = $connection->query($query, [$this->origin, $this->destination, $this->dep_date, $this->return_date]);
        
        if ( $rows ) {
            $this->cost = $rows[0]['cost'];
            $this->duration = $rows[0]['duration'];
            
            return true;
        }
        
        return false;
    }

    public function cache($connection) {
        $query = "INSERT INTO trips (origin, destination, departureDate, returnDate, duration, cost, timePulled)
                  VALUES (?,?,?,?,?,?, NOW())";
        $num = $connection->insert($query,[$this->origin, $this->destination, $this->dep_date, $this->return_date,
                            $this->duration, $this->cost]);
        return $num;
    }

    public function get_average($connection) {
        $query = "SELECT avg(duration) duration, avg(cost) cost FROM trips where origin=? and destination=?";
        $result = $connection->query($query, [$this->origin, $this->destination]);
        if ( $result ) {
            $this->avg_cost = $result[0]['cost'];
            $this->avg_duration = $result[0]['duration'];
        }
        
        return 0;
    }

    public function to_array() {
        return [
            'cost' => $this->cost,
            'avg_cost' => $this->avg_cost,
            'avg_duration'=> $this->avg_duration,
            'duration' => $this->duration
        ];
    }
}
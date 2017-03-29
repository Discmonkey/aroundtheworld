<?php

/**
 * Created by PhpStorm.
 * User: mgrinchenko
 * Date: 3/27/2017
 * Time: 12:30 PM
 */

# this class was created to manage all functions related to airports, it functions as quasi model and also
# includes useful methods such as finding the distance to another airport, and converting one self to an associative
# array that can then be inserted directly into a queue
class Airport
{
    public $id = 0;
    public $city = '';
    public $country = '';
    public $lat = '';
    public $long = '';
    public $altitude = '';
    public $timezone = '';
    public $IATA = '';
    public $ICAO = '';
    public $distance_from_source = 0;
    
    public static function load_from_sql($row): Airport{
        $airport = new Airport();
        $airport->id = $row['id'];
        $airport->city = $row['city'];
        $airport->country = $row['country'];
        $airport->lat = $row['latitude'];
        $airport->long = $row['longitude'];
        $airport->altitude = $row['altitude'];
        $airport->timezone = $row['timezone'];
        $airport->IATA = $row['IATA'];
        $airport->ICAO = $row['ICAO'];

        return $airport;
    }
    
    public function set_distance($mdl) {
        $this->distance_from_source = self::get_distance($this, $mdl);
    }

    public static function bulk_load_from_ids($id_arr, $connection) {
        // return multiple airportMdls in the same call, maintaining order is important
        $id_list = '(' . implode(',', $id_arr) . ')';
        $order_list = ' order by field (id,' . implode(',', $id_arr) . ')';
        
        $query = "SELECT * FROM airports where id in ".$id_list . $order_list;

        $rows = $connection->query($query,[]);

        return array_map(array('Airport', 'load_from_sql'), $rows);
    }

    public static function get_distance($mdl1, $mdl2) {
        # find the distance in miles between two airports using the haversine formula
        $earth_radius = 3959.0;

        $lat_rad_1 = self::to_rads($mdl1->lat);
        $lat_rad_2 = self::to_rads($mdl2->lat);

        $long_rad_1 = self::to_rads($mdl1->long);
        $long_rad_2 = self::to_rads($mdl2->long);

        $lat_dif = $lat_rad_2 - $lat_rad_1;
        $long_dif = $long_rad_2 - $long_rad_1;

        $term_1 = sin($lat_dif / 2) * sin($lat_dif / 2);
        $term_2 = cos($lat_rad_1) * cos($lat_rad_2) * sin($long_dif / 2) * sin($long_dif / 2);
        $term_3 =  2 * atan2(sqrt($term_1 + $term_2), sqrt(1 - $term_1 - $term_2));

        return $term_3 * $earth_radius;

    }

    public static function to_rads($deg) {
        $pi = pi();
        return $deg * ($pi / 180);
    }

    public function to_array() {
        return [
            'city' => $this->city,
            'IATA' => $this->IATA,
            'distance' => $this->distance_from_source,
            'country' => $this->country,
        ];
    }
}
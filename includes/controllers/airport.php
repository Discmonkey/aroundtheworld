<?php

/**
 * Created by PhpStorm.
 * User: mgrinchenko
 * Date: 3/27/2017
 * Time: 12:33 PM
 */
include '../models/airport.php';
class AirportCtrl
{
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
}
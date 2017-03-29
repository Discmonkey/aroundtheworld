<?php

/**
 * Created by PhpStorm.
 * User: mgrinchenko
 * Date: 3/27/2017
 * Time: 12:30 PM
 */
class AirportMdl
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

    public static function load_from_sql($row): AirportMdl{
        $airport = new AirportMdl();
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

    public static function bulk_load_from_ids($id_arr, $connection) {
        // return multiple airportMdls in the same call
        $id_list = '(' . implode(',', $id_arr) . ')';
        $query = "SELECT * FROM airports where id in ".$id_list;

        $rows = $connection->query($query);

        return array_map('AirportMdl::load_from_sql', $rows);
    }
}
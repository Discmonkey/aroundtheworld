<?php

/**
 * Created by PhpStorm.
 * User: mgrinchenko
 * Date: 3/28/2017
 * Time: 5:55 PM
 */

# this class interfaces with the google QPX api
# it is responsible for finding the optimal cost of a potential trip, and loading that information into a Trip
# class that it is passed. The class only supports one public function, however the three internal functions break each
# call into its logical components; namely building the request, making the request, and parsing the request. 

class QPX
{
    private $api_token = 'AIzaSyAxoz0khU7ihusLqctbei146AxDFDhB5rI';

    private function build_request($trip) {
        $base_url = "https://www.googleapis.com/qpxExpress/v1/trips/search?key=";
        $url_with_auth = $base_url.$this->api_token;
        $params = [
            'request'=> [
                "passengers"=> [
                    "adultCount"=> 1,
                    "infantInLapCount"=> 0,
                    "infantInSeatCount"=> 0,
                    "childCount"=> 0,
                    "seniorCount"=> 0
                ],
                "slice"=> [
                    [
                        "origin" => $trip->origin,
                        "destination" => $trip->destination,
                        "date" => $trip->dep_date
                    ],
                    [
                        "origin" => $trip->destination,
                        "destination" => $trip->origin,
                        "date" => $trip->return_date
                    ]
                ],
                "solutions" => 1,
                "refundable" => false
            ]
        ];

        return ['url'=> $url_with_auth, 'params'=>$params];
    }

    private function make_request($url, $params) {
        return Requests::post($url,['Content-Type'=>'application/json'], json_encode($params));
    }

    private function parse_response($response, &$trip) {
        # this function will eventually be broken out into its own class to provide greater flexibility,
        # for now I am assuming that every trip is a round trip, which may not be true in the future
        # a trip is initialized with default values success=false,
        if ($response->success) {

            $body = json_decode($response->body, true);
            $trip_api = $body['trips']['tripOption'][0];
            $trip->duration = $body['trips']['tripOption'][0]['slice'][0]['duration'] + 
                $body['trips']['tripOption'][0]['slice'][1]['duration'];
            $trip->cost = $this->get_cost($trip_api['saleTotal']);
            $trip->success = true;
        } 
        
        return $response->success;
    }

    private function get_cost($cost_string) {
        // If we cannot read in the value we want to return a relatively high price, as returning zero would disrupt
        // our ranking algorithm
        if (  strpos($cost_string, 'USD') == 0) {
            return floatval(str_replace('USD', '', $cost_string));
        }

        return 100000.0;
    }

    public function get_plan(&$trip) {
        $request = $this->build_request($trip);
        $response = $this->make_request($request['url'], $request['params']);
        return $this->parse_response($response, $trip);


    }
}
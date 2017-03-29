<?php

/**
 * Created by PhpStorm.
 * User: mgrinchenko
 * Date: 3/28/2017
 * Time: 5:55 PM
 */
include "../Requests.php";

class QPX
{
    private $api_token = 'AIzaSyAxoz0khU7ihusLqctbei146AxDFDhB5rI';

    private function build_request($dep_point, $arr_point, $dep_date, $arr_date) {
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
                        "origin" => $dep_point,
                        "destination" => $arr_point,
                        "date" => $dep_date
                    ],
                    [
                        "origin" => $arr_point,
                        "destination" => $dep_point,
                        "date" => $arr_date
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

    private function parse_response($response, $departure, $arrival, $departure_date, $arrival_date) {
        # this function will eventually be broken out into its own class to provide greater flexibility,
        # for now I am assuming that every trip is a round trip, which may not be true in the future
        if ($response->success) {

            $body = json_decode($response->body, true);
            $trip = $body['trips']['tripOption'][0];
            $duration = $body['trips']['tripOption'][0];
            $cost = $this->get_cost($trip['saleTotal']);
            return [
                'duration'=> $duration,
                'cost'=> $cost,
                'departure'=> $departure,
                'arrival'=> $arrival,
                'departure_date'=> $departure_date,
                'arrival_date'=> $arrival_date
            ];

        } else {
            return 0;
        }
    }

    private function get_cost($cost_string) {
        // If we cannot read in the value we want to return a relatively high price, as returning zero would disrupt
        // our ranking algorithm
        if (  strpos($cost_string, 'USD') == 0) {
            return floatval(str_replace('USD', '', $cost_string));
        }

        return 100000.0;
    }

    public function get_plan($dep_point, $arr_point, $dep_date, $arr_date) {
        $request = $this->build_request($dep_point, $arr_point, $dep_date, $arr_date);
        $response = $this->make_request($request['url'], $request['params']);
        return $this->parse_response($response,$dep_point, $arr_point, $dep_date, $arr_date);


    }
}
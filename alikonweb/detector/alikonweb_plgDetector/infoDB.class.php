<?php

//Get IP location from ipinfodb.com
//
//06/06/2010 10.11
defined('_JEXEC') or die;

class IpInfoDb {

    private $apikey = null;
    private $ip = null;

    /**
     * 	@param	string	$email	The mail.
     * 	@param	string	$ip	the ip.
     */
    public function __construct($apikey, $ip) {
        $this->apikey = $apikey;
        $this->ip = $ip;

        // Set some default values
    }

    function ipLocation() {
        $d = @file_get_contents("http://api.ipinfodb.com/v3/ip-city/?key=" . $this->apikey . "&ip=" . $this->ip."&format=xml");
        $response = array('status' => 'ko', 'latitude' => '0', 'longitude' => '0', 'zippostalcode' => '', 'city' => 'unknown', 'region_name' => '', 'country_name' => 'Unknown', 'country_code' => 'UN', 'ip' => $this->ip);
        //Use backup server if cannot make a connection
     
        if (!$d) {


            return array('status' => 'ko', 'latitude' => '0', 'longitude' => '0', 'zippostalcode' => '', 'city' => 'unknown', 'region_name' => '', 'country_name' => 'Unknown', 'country_code' => 'UN', 'ip' => $this->ip);
        } else {
            $answer = @new SimpleXMLElement($d);
           
            if ($answer->statusCode == 'ERROR') {
                return array('status' => strtolower($answer->Status), 'latitude' => '0', 'longitude' => '0', 'zippostalcode' => '', 'city' => 'INVALID API KEY', 'region_name' => '', 'country_name' => 'Unknown', 'country_code' => 'UN', 'ip' => $this->ip);
            }
            if ($answer->statusCode == 'OK') {
                $country_code = $answer->countryCode;
                $country_name = $answer->countryName;
                $region_name = $answer->regionName;
                $city = $answer->cityName;
                $zippostalcode = $answer->zipCode;
                $latitude = $answer->latitude;
                $longitude = $answer->longitude;
            }

            //Return the data as an array
            return array('status' => strtolower($answer->Status), 'latitude' => $latitude, 'longitude' => $longitude, 'zippostalcode' => $zippostalcode, 'city' => $city, 'region_name' => $region_name, 'country_name' => $country_name, 'country_code' => $country_code, 'ip' => $this->ip);
        }


        return $response;
    }

}
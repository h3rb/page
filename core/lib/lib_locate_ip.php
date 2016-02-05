<?php

function get_ip_info( $ip="74.125.45.100", $type="city" ) {
 $api_key = "036d959260437aac4f565eb7e3ec94795d9bbfa2997196ead58820e9f672d33f";
 $response = file_get_contents( "http://api.ipinfodb.com/v3/ip-$type/?key=$api_key&ip=$ip" );
 return $response;
}

function decode_ip_info( $response ) {
 $response =  explode(";",$response);
 return array(
    "result" => $response[0],
    "err_code" => $response[1],
    "ip" => $response[2],
    "country_code" => $response[3],
    "country_name" => $response[4],
    "state" => $response[5],
    "city" => $response[6],
    "zip" => $response[7],
    "lat" => $response[8],
    "long" => $response[9],
    "gmt" => $response[10] );
};


//var_dump( get_ip_info() );

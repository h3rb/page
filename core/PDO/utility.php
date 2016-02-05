<?php

/************************************************************** Author: H. Elwood Gilliland III
 *  _____  _                 _____       _                    *
 * |  _  ||_| ___  ___  ___ |     | ___ | |_  ___  ___        * (c) 2015 PieceMaker Technologies
 * |   __|| || -_||  _|| -_|| | | || .'|| '_|| -_||  _|       * ---------------------------------
 * |__|__ |_||___||___||___||_|_|_||__,||_,_||____|_|         * Utility functions for processing
 * |_   _| ___  ___ | |_  ___  ___ | | ___  ___ |_| ___  ___  * requests in the Aggregation Service
 *   | |  | -_||  _||   ||   || . || || . || . || || -_||_ -| *
 *   |_|  |___||___||_|_||_|_||___||_||___||_  ||_||___||___| *
 *                                         |___|              *
 **************************************************************/

if ( !function_exists('num_to_name') ) {
// Returns an enumeration name based on its numeric code
function num_to_name( $arr, $num ) {
 return $arr[$num];
}
}

if ( !function_exists('name_to_num') ) {
// Returns an enumeration's number based on its string name (exact match case insensitive)
function name_to_num( $arr, $name ) {
 foreach ( $arr as $k=>$v ) if ( stripos($name,$v) == 0 && strlen($name) == strlen($v) ) return $k;
 return FALSE;
}
}

if ( !function_exists('software_version_code') ) {
// Generates a version code based on a timestamp
function software_version_code( $timestamp ) {
 return date('m-d-Y-h-i-s-a',$timestamp);
}
}

if ( !function_exists('human_date') ) {
 // Generates a human-readible date
 function human_date( $ts ) { return date('n-j-Y',intval($ts)); }
}

if ( !function_exists('human_time') ) {
 // Generates a human-readible time
 function human_time( $ts ) { return date('g:i:s a',intval($ts)); }
}

if ( !function_exists('human_datetime') ) {
 // Generates a human-readible date/time
 function human_datetime( $ts ) { return date('n-j-Y g:i:s a',intval($ts)); }
}

if ( !function_exists('human_datetime_split') ) {
 // Generates a human-readible date/time
 function human_datetime_split( $ts ) {
  return human_time($ts).'<BR>'.human_date($ts);
 }
}

if ( !function_exists('getpost') ) {
// Combines get and post into a single array, duplicates overwrite
function getpost() {
 $a=array();
 foreach ( $_GET  as $k=>$v ) $a[$k]=$v;
 foreach ( $_POST as $k=>$v ) $a[$k]=$v;
 return $a;
}
}

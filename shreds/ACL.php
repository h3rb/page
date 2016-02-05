<?php

 abstract class ACL {

  static public function has( $haystack, $needle, $delim=',' ) {
   return tagged($haystack,$needle,$delim);
  }

  static public function set( $haystack, $needle,$delim=',' ) {
   if ( has($haystack,$needle) ) return $haystack;
   return $haystack.$delim.$needle;
  }

  static public function remove( $haystack, $needle, $delim=',' ) {
   $words=words($haystack,$delim);
   $out="";
   $last=count($words);
   foreach ( $words as $word )
    if ( matches($needle,$word) ) continue;
     else $out.=$word.$delim;
   return rtrim($out,$delim);
  }

 };

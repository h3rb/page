<?php // json_* class wrapper

 class JSON {
  public function __construct() {}
  public function encode( $value ) {
   $result=json_encode($value);
   $this->Errors('encode');
   return $result;
  }
  public function decode( $encoded ) {
   $result=json_decode($encoded);
   $this->Errors('decode');
   return $result;
  }
  public function Errors( $caller ) {
   switch ( json_last_error() ) {
    case JSON_ERROR_NONE: return;
    default: plog('JSON->'.$caller.'(): '.json_last_error_msg() ); return;
   }
  }
 };

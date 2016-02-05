<?php

 class Auth extends Model {

  function Construct() {
   global $auth_database;
   $this->db=$auth_database;
  }

  function User( $a_id ) {
   global $auth_model;
   return $auth_model->Get($a_id);
  }

  function byUsername( $un ) {
   plog('Find user: '.$un);
   $result=$this->Select( array( 'username'=>$un ) );
   if ( is_array($result) && count($result) == 1 ) return $result[0];
   return NULL;
  }

  function byProfile( $p_id ) {
   $result=$this->Select( array( 'r_Profile'=>$p_id ) );
   if ( is_array($result) && count($result) == 1 ) return $result[0];
   return NULL;
  }

  function ExpirePassword( $auth ) {
   if ( !false_or_null($auth) ) {
    $this->Set( $auth['ID'], array( $auth['password_expiry']=>strtotime('now') ) );
    return true;
   } else return false;
  }

  function PasswordEncrypt( $a ) {
   return ourcrypt($a);
  }

  function PasswordMatches( $a, $b ) {
   return matches($a,$b,TRUE);
  }

  static function ACL( $required ) {
   global $auth,$auth_database;
   if ( !is_array($auth) ) return FALSE;
   if ( !isset($auth['acl']) ) return FALSE;
   plog('Checking ACL: '.(is_array($required)?implode(',',$required):$required));
   return ACL::has($auth['acl'],$required);
  }

 };

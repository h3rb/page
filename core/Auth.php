<?php

 if ( isset($_SERVER['REMOTE_ADDR']) ) plog('----Request from: '.$_SERVER['REMOTE_ADDR']);

 // Basic (minimal) bootstrapping.
 include_all(SITE_ROOT.'/model/');
 // We're done!


//
// Execute the authentication (any page included will perform these operations)
// Pages with the constant OPEN defined at the top will not perform these.


 global $auth_database;
 try {
 $auth_database=new Database(
  AUTH_DB_DSN,
  AUTH_DB_USER,
  AUTH_DB_PASS
 );
 } catch (Exception $e) { plog($e); }

 plog('$auth_database: '.vars($auth_database));

 global $auth_model;    $auth_model=new Auth($auth_database);
 global $session_model; $session_model=new Session($auth_database);
 global $profile_model; $profile_model=new Profile($auth_database);

 global $auth;          $auth=NULL;
 global $session;       $session=NULL;
 global $user;          $user=NULL;

 global $domain;        $domain=NULL;
 global $expired;       $expired=false;
 global $pageurl;       $pageurl=current_page_url();
 global $is_logged_in;  $is_logged_in=false;

 if ( !defined('quiet_auth') ) {
  $domain  = explode( "/", str_replace( "http://", "", $pageurl ) );
  $domain  = $domain[0];
 }

 if ( !defined('suppress_auth') ) {
  $session_model->Active();
  plog( '$auth: '.vars($auth) );
  plog( '$session: '.vars($session) );
 }

 global $plog_level;
 if ( $plog_level == 1 ) {
  plog('##### $pageurl: '.vars($pageurl));
  if ( isset($_SERVER['HTTP_REFERRER']) ) plog('Referred: '.$_SERVER['HTTP_REFERRER']);
  plog('getpost():------'.vars(getpost()));
 }

 plog('----Executing: '.vars($pageurl));

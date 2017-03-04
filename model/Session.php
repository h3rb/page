<?php

 include_once SITE_ROOT.'/core/lib/lib_locate_ip.php';

 global $session_refreshed; $session_refreshed=FALSE;

 class Session extends Model {

  var $data;
  public function Construct() {
   $this->data=array();
  }

  // Creates a new session
  public function Create( $r_Auth ) {
   plog("Session::Create for User ID: ".$r_Auth);
   $now=strtotime('now');
   $data=array(
    "login"    => $now,
    "REFERRER" => getenv('HTTP_REFERER'),
    "IP"       => getenv('REMOTE_ADDR'),
//    "ip_info"  => get_ip_info( getenv('REMOTE_ADDR') ),
    "BROWSER"  => getenv('HTTP_USER_AGENT'),
    "r_Auth"   => $r_Auth,
    "status"   => 1,
    "refreshed"=> $now,
    "last_refreshed"=> $now
   );
   global $session_id,$auth;
   $session_id=$this->Insert( $data );
   plog('New session ID: '.vars($session_id));
   cook( "username", $auth['username'], timeout );
   cook( "session",  $session_id, timeout );
   return $session_id;
  }

  public function LoggedOut( $session ) {
   return intval($session['status'])===0 || $this->Timedout($session) || intval($session['logout']) > 0;
  }

  public function Timedout( $session ) {
   return (strtotime('now')-intval($session['refreshed']) >= timeout);
  }

  public function ActiveUsers() {
   $users=$this->Select('status=1');
   $active=array();
   foreach ($users as $session) {
    if ( !$this->LoggedOut($session) ) $active[]=$session;
    else $this->Set($session['ID'],array('status'=>0));
   }
   return $active;
  }

  public function Refresh( $session ) {
   global $no_session_refresh;
   global $session_refreshed;
   if ( $session_refreshed === FALSE )
   if ( $no_session_refresh === TRUE ) {} else {
    $this->Set( $session['ID'],
     array( "last_refreshed" => $session['refreshed'],
           "refreshed" => strtotime('now'),
           "requests" => intval($session['requests'])+1
     )
    );
    global $database;
    $a_model=new Auth($database);
    $a=$a_model->Get($session['r_Auth']);
    cook( "username", $a['username'], timeout );
    cook( "session",  $session['ID'], timeout );
   }
   $session_refreshed=TRUE;
  }

  // Tests if there is a current session, called in the bootstrap
  public function Active( $refresh=TRUE ) {
   global $session_model,$auth_model,$profile_model;
   global $is_logged_in;
   global $session;
   global $user;
   global $auth;
   $session=$this->Get( base64_decode($_COOKIE['session']) );
   if ( !is_array($session) || !isset($session['r_Auth']) ) return ($is_logged_in=false);
   if ( $this->LoggedOut($session) ) return ($is_logged_in=false);
   $auth=$auth_model->Get( $session['r_Auth'] );
   if ( !is_array($auth) || !isset($auth['r_Profile']) ) return ($is_logged_in=false);
   else $user=$profile_model->Get( $auth['r_Profile'] );
   if ( !is_array($user) ) return ($is_logged_in=false);
   if ( $auth_model->ACL('locked') ) {
    plog('Account is locked, logging user '.$auth['ID'].' off.');
    $this->Logout();
    $is_logged_in=false;
    Page::Redirect("login?m=4");
   }
   $this->Refresh($session);
   $url=$_url();
   // Ignore any ajaxy stuff
   if ( stripos($url,"ajax.") === FALSE )
    $this->Set( $session['ID'], array( 'last_url'=>current_page_url() ) );
   return ($is_logged_in=true);
  }
  
    // Tests if there is a current session, called when you don't have a cookie to depend on (mobile client)
  public function isActive( $id, $refresh=TRUE ) {
   global $session_model,$auth_model,$profile_model;
   global $is_logged_in;
   global $session;
   global $user;
   global $auth;
   $session=$this->Get( $id );
   if ( !is_array($session) || !isset($session['r_Auth']) ) return ($is_logged_in=false);
   if ( $this->LoggedOut($session) ) return ($is_logged_in=false);
   $auth=$auth_model->Get( $session['r_Auth'] );
   if ( !is_array($auth) || !isset($auth['r_Profile']) ) return ($is_logged_in=false);
   else $user=$profile_model->Get( $auth['r_Profile'] );
   if ( !is_array($user) ) return ($is_logged_in=false);
   if ( $auth_model->ACL('locked') ) {
    plog('Account is locked, logging user '.$auth['ID'].' off.');
    $this->Logout();
    return ($is_logged_in=false);
   }
   $this->Refresh($session);
   $url=current_page_url();
   // Ignore any ajaxy stuff
   if ( stripos($url,"ajax.") === FALSE ) $this->Set( $session['ID'], array( 'last_url'=>current_page$
   return ($is_logged_in=true);
  }

  static function logged_in() { global $is_logged_in; return $is_logged_in; }

  // Redirect a user if they don't meet the security requirements for this page.
  function Security( $logged_in, $where_to_redirect="login", $acl=FALSE ) {
   global $is_logged_in;
   if ( boolval($logged_in) !== boolval($is_logged_in) ) {
    header( "Location: $where_to_redirect");
    return FALSE;
   }
   if ( $acl !== FALSE ) {
    global $auth;
    if ( Auth::ACL($auth['acl'],$acl) === FALSE ) {
     header( "Location: $where_to_redirect");
     return FALSE;
    }
   }
   return TRUE;
  }

 function username_cleaner($s) {
  return str_replace( "'", "''", strtolower($s) );
 }

 function username_is_not_clean($s) {
  $s=strtolower($s);
  if ( preg_match( "/ /", $s ) > 0 ) return true;
  if ( preg_match( "/'/", $s ) > 0 ) return true;
  if ( preg_match("/[^-a-z0-9_.-]/i", $name) ) return false;
 }

 // Debug function
 function get_session( $get_new_cookies=false, $reporting=false ) {
  if ( $get_new_cookies === true ) $this->Active(true);
  if ( $reporting === true ) $this->print_debug_info();
 }

 // Debug function
 function print_debug_info( $sid="none in this context" ) {
  plog( '---------' );
  global $user;
  plog( '$_SESSION' );
  plog( $_SESSION );
  plog( '$_COOKIE' );
  plog( $_COOKIE );
  $logged_in=$this->check_cookie(true);
  plog('print_debug_info:');
  plog( 'logged in: ' . ($logged_in ? "Yes" : "No") );
  plog( 'User:' );
  plog( $user );
  plog( 'Session (superglobal,b64): ' . $_SESSION['session'] );
  plog( 'Database Session Entry:' );
  global $session_model;
  $session = $session_model->Get( base64_decode( $_SESSION['session'] ) );
  plog( $session );
  plog( '---------' );
 }

 /*
 function is_logging_out() {
  global $session_id;
  if ( !is_null($session_id) ) { $sid=$session_id; } else return FALSE;
  global $session_model;
  global $session;
  clear_cookie();
  $session = $session_model->Get( $sid );
  if ( is_null( $session ) ) { return FALSE; } else {
   if ( intval($session['status'])==1 ) {
    $session_model->Set( $sid, array( "status"=>0, "logout"=>strtotime('now') ) );
    return TRUE;
   }
  }
 }*/

 function Logout( $sess=-1 ) {
  if ( $sess=== -1 ) {
  global $session_id;
  if ( is_null($session_id) ) return FALSE;
  $sid=$session_id;
  global $session_model,$session;
  $session = $session_model->Get( $sid );
  } else $session=$sess;
  if ( is_null( $session ) ) { return FALSE; } else {
   // Turn off the session's activity indicator
   if ( !$this->LoggedOut($session) )
    $this->Set($session['ID'], array( 'status'=>0, 'logout'=>strtotime('now') ) );
  }
 }

 private function check_cookie( $report=false ) {
   if ( $report === true ) plog( 'check_cookie(): ');
   // Check for the session variable or the appropriate cookie information.
   if ( !isset($_SESSION['username'])
     || is_null($_SESSION['username'])
     || strlen(trim($_SESSION['username'])) == 0 ) {
    $ID       = base64_decode( $_COOKIE['session']  );
    $username = base64_decode( $_COOKIE['username'] );
   } else {
    $username = base64_decode( $_SESSION['username'] );
    $ID       = base64_decode( $_SESSION['session']  );
   }

   if ( $report === true ) $this->print_debug_info();

   // Determine if the session should has expired this page load.
   global $expired;
   $expired = false;

   // This is an expired session.
   if ( strlen($ID)==0 || strlen($username)==0 ) {
    $expired = true;
    return FALSE;
   }

   // Garner the valid session information.
   global $session_id;
   $session_id=$ID;

   global $session;
   $session = $session_model->Get($ID);
   plog('check_cookie: session='.var_export($session,true));

   // Invalid or expired session
   if ( false_or_null($session) || !is_array($session) ) {
    $expired = true;
    return FALSE;
   }

   // Test for inactivity timeout.
   if ( $this->Timedout($session) ) {
    $this->Logout();
    $expired = true;
    return FALSE;
   }

   // Did we already log out?
   if ( $this->LoggedOut($session) ) {
    $expired = true;
    return FALSE;
   }

   $this->Refresh($session);

   global $auth_model;
   global $auth;
   $auth=$auth_model->Get( intval($session['r_Auth']) );

   // If we delete the user's Auth record, the session is terminated.
   if ( false_or_null($auth) ) {
    $this->Logout();
    $expired = true;
    return FALSE;
   }

   // Grab the ancillary user profile information based on the session and auth pair.
   global $profile_model;
   global $user;
   $user=$profile_model->Get( $auth['r_Profile'] );

   // Talk if we're debugging.
   if ( $report === true ) $this->print_debug_info();
   if ( $report === true ) plog( 'check_cookie(): (end)');
   return TRUE;
  }

 };


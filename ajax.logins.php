<?php

 global $no_session_refresh; $no_session_refresh=TRUE;
 include 'core/Page.php';

 $url_map=array(
  "/ranged.csv"=>"Data",
  "/inspect.session"=>"Session",
  "/inspect.session.id"=>"Session",
  "/file"=>"FILES",
 );
 function get_url_map($s,$arr) {
  foreach ( $arr as $k=>$v ) if ( stripos($s,$k) !== FALSE ) return $v;
  $s=basename($s);
  $s=explode(".",$s);
  $s=$s[0];
  return $s;
 }

 global $auth_database;

 $m_sess=new Session($auth_database);
 global $session;
 if ( $m_sess->LoggedOut($session) ) { echo '0'; die; }

 $sessions=$m_sess->ActiveUsers();

 $m_auth=new Auth($auth_database);

 $mod_model=new Modification($auth_database);

 $result=array();
 $was=array();
 foreach ( $sessions as $s ) if ( !$m_sess->LoggedOut($s) ) {
  $activity=' ';
  $activity.=get_url_map($s['last_url'],$url_map);
  if ( matches($activity,'request_login.php') ) continue;
  $a=$m_auth->Get($s['r_Auth']);
  if ( false_or_null($a) ) continue;
  $found=FALSE;
  foreach ( $was as $u ) if ( matches($u,$a['username']) ) { $found=TRUE; break; }
  if ( $found === TRUE ) continue;
  $was[]=$a['username'];
  $mod=$mod_model->Select(array('r_Auth'=>$a['ID']),'*',"",'ORDER BY Timestamp DESC',1);
  if ( !false_or_null($mod) ) {
   $lastmod=(strtotime('now')-intval($mod[0]['Timestamp']))/60;
   if ( intval($lastmod) > 0 ) $lastmod=intval($lastmod).'m';
   else $lastmod='';
  } else $lastmod='';
  if ( strlen(trim($activity)) == 0 ) $activity=' <span class="fi-skull"></span> '.(strtotime('now')-$s['refreshed']).'s'.' '.$lastmod.'</small>';
  else $activity='<small>'.'<span class="bottomtinytag">'.$activity.'</span> '.(strtotime('now')-$s['refreshed']).'s'.' '.$lastmod.'</small>';
  $result[]=trim('<b>'.$a['username'].'</b> '.$activity);
 }
 $result=array_unique($result);
 $result=implode(' | ',$result);
 echo /*'Logged in: '.*/rtrim($result,',');


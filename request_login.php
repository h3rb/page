<?php
//global $plog_level; $plog_level=1;
include 'core/Page.php';
plog('File: '.__FILE__);
global $session_model, $auth_model, $auth;
$getpost=getpost();
if ( !(isset($getpost['username']) && isset($getpost['password'])) ) Page::Redirect("login?m=1");
$auth=$auth_model->byUsername($getpost['username']);
plog('$getpost: '.vars($getpost));
plog('$auth: '.vars($auth));
if ( !is_array($auth) ) Page::Redirect("login?m=2");
if ( strlen($auth['password']) == 0
  || matches(ourcrypt($getpost['password']),$auth['password']) ) {
 plog('Password matched!  User has authenticated.');
 if ( Auth::ACL('locked') ) {
  plog('Account is locked, logging user '.$auth['ID'].' off.');
  $session_model->Logout();
  Page::Redirect("login?m=4"); die;
 }
 $session_model->Create($auth['ID']);
 Page::Redirect("dash");
} else {
 Page::Redirect("login?m=1");
}

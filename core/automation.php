<?php // Include this file instead of core/Page.php when executing from shell.

/*********************************************************************************************
 *  __    __________________   ________________________________   __________  ________       *
 * /\ \  /\  __ \  ___\__  _\ /\  __ \  ___\__  _\  == \  __ \ "-.\ \  __ \ \/\ \__  _\ (tm) *
 * \ \ \_\_\ \/\ \___  \/\ \/ \ \  __ \___  \/\ \/\  __<\ \/\ \ \-.  \  __ \ \_\ \/\ \/      *
 *  \ \_____\_____\_____\ \_\  \ \_\ \_\_____\ \_\ \_\ \_\_____\_\\"\_\_\ \_\_____\ \_\      *
 *   \/_____/_____/_____/\/_/   \/_/\/_/_____/\/_/\/_/\/_/_____/_/ \/_/_/\/_/_____/\/_/      *
 *    --------------------------------------------------------------------------------       *
 *     Page Framework (c) 2007-2016 H. Elwood Gilliland III                                  *
 *********************************************************************************************
 * This software is copyrighted software.  Use of this code is given only with permission to *
 * parties who have been granted such permission by its author, Herbert Elwood Gilliland III *
 *********************************************************************************************/

 if ( PHP_SAPI === 'cli' ) {

 include_once 'path.php';
 include_once 'utility.php';
 include_once 'unique.php';
 include_once 'engines.php';
 include_once 'root.php';
 include_once 'Database.php';
 include_once 'ui.php';

 // Basic (minimal) bootstrapping.
 include_once SITE_ROOT.'/settings/config.php';
 include_once SITE_ROOT.'/settings/config.flags.php';
 include_once SITE_ROOT.'/settings/config.enums.php';
 include_once SITE_ROOT.'/settings/config.global.php';
 include_once SITE_ROOT.'/settings/config.databases.php';
 include_all(SITE_ROOT.'/model/');


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

 global $database; // change to something else if you want a common auth
 $database=$auth_database; 
 
 global $db;
 $db=$database;

 plog('----Executing.');

 // We're done!

 }


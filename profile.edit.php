<?php

 include 'core/Page.php';

 if ( !Session::logged_in() ) Page::Redirect('login');
 if ( !Auth::ACL("admin") && !Auth::ACL("su") ) Page::Redirect('dash');

 $p= new Page;
 if ( !$p->ajax ) $p->HTML('header.html',array("###MENU###"=>Dropdown_menu($p)));

 $p->title ="Your Website";
 $p->CSS( "main.css" );
 $p->Jquery();

 $getpost=getpost();
 if ( !isset($getpost['ID']) ) Page::Redirect('dash');
 if ( isset($getpost['new']) ) {
  $p->JS('notifier.js');
  $p->JS('Notifier.info("Editing new Item");');
 }
 $p->HTML('<BR>');

 global $auth_database;

 $profile_model=new Profile($auth_database);
 $auth_model=new Auth($auth_database);

 $a=$auth_model->Get($getpost['ID']);
 if ( false_or_null($a) ) Page::Redirect('profiles');
 $pro=$profile_model->Get($a['r_Profile']);
 if ( false_or_null($pro) ) Page::Redirect('profiles');


 $p->HTML('<span class="breadcrumbs">Editing Profile #'.$pro['ID'].' and Auth #'.$a['ID'].'</span>');
 $p->HTML('<span class="breadcrumbs-link"><a href="profiles" class="bare">&larr; Profiles</a></span>');

 $p->Bind_LoadPlugins();

 $p->HTML('<div class="formboundary">');

 $p->HTML('<div class="formgroup wide">');
 $p->HTML('Username');
 $p->BindString( "Auth", $a['ID'], "username", $a['username'], "Enter username", FALSE, "texty" );
 $p->HTML('</div>');
 $p->HTML('<div class="formgroup wide">');
 $p->HTML('First');
 $p->BindString( "Profile", $pro['ID'], "first_name", $pro['first_name'], "Enter first", FALSE, "texty" );
 $p->HTML('Last');
 $p->BindString( "Profile", $pro['ID'], "last_name", $pro['last_name'], "Enter first", FALSE, "texty" );
 $p->HTML('</div>');
 $p->HTML('<div class="formgroup wide">');
 $p->HTML('Email');
 $p->BindString( "Profile", $pro['ID'], "email", $pro['email'], "Enter email", FALSE, "texty" );
 $p->HTML('</div>');

 $p->HTML('<a href="profile.reset.password?ID='.$a['ID'].'" class="bare buttonlink">Clear User Password</a>');

 $p->HTML('<div class="formgroup wide">');
 $p->HTML('ACL ');
 $p->Tips( '<span class="fi-pricetag-multiple"></span>', "Access Control Level Tags", array(
  "admin"=>"Adds user to the administrator group permitting access control modification of other users",
  "su"=>"Superuser - typically only 1 user with user ID 1, the `persona` of automation",
  "locked"=>"Account cannot be logged in",
  "legacy"=>"Imported account from a prior version",
  "Type in the above keyword to this tags section and hit return to activate.  Each tag needs only to appear once to activate the feature.  Subsequent tags are ignored."
 ));
 $p->BindStringTags( "Auth", $a['ID'], "acl", $a['acl'] );
 $p->HTML('</div>');

 $p->HTML('</div>');

 // Footer
 if ( !$p->ajax ) $p->HTML('footer.html');
 $p->Render();

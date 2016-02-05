<?php

 include 'core/Page.php';

 if ( !Session::logged_in() ) Page::Redirect('login');
 if ( !Auth::ACL("admin") && !Auth::ACL("su") ) Page::Redirect('dash');

 global $auth_database, $auth;

 $profile_model=new Profile($auth_database);
 $p_id=$profile_model->Insert(array(
  'first_name'=>"John",
  'last_name'=>'Dowe',
 ));

 $auth_model=new Auth($auth_database);
 $new_id=$auth_model->Insert(array(
  'username'=>'new',
  'acl'=>'locked',
  'r_Profile'=>$p_id,
 ));

 Page::Redirect('profile.edit?new=1&ID='.$new_id);

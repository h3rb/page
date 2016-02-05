<?php

// global $plog_level; $plog_level=1;
 include 'core/Page.php';

 if ( !Session::logged_in() ) {
  redirect('login');
 }

 if ( !Auth::ACL("su") ) {
  redirect('dash');
 }

 $p = new Page();






 $p->Render();

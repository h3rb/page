<?php
 include 'core/Page.php';

 if ( Session::logged_in() ) {
  redirect('dash');
 } else {
  redirect('login');
 }

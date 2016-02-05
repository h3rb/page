<?php

 include 'core/Page.php';

 global $session_model,$session;
 if ( !is_null($session) && !$session_model->LoggedOut($session) ) {
  $session_model->Logout($session);
 }
 Page::Redirect("login");

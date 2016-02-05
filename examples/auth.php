<?php

 include 'core/Page.php';

 $p=new Page();

 $p->HTML('<b>It works!</b>');

 global $profile_model; // Model for non-auth user settings like name, email, dashboard, etc.
 global $user;          // Current active user profile (or NULL if none)
 global $session_model; // Model for user session history
 global $session;       // Current active session (or NULL if none)
 global $auth_model;    // Auth model for user authentication information (username for example)
 global $auth;          // Current active auth profile (or NULL if none)
 global $logged_in;     // true if user is logged in and authenticated, false otherwise

 $p->Render();

 var_dump($user);
 var_dump($session);
 var_dump($auth);
 var_dump($logged_in);
 var_dump($acl);

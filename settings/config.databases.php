<?php

// Set the site's Auth DB here

define('AUTH_DB_DSN', 'mysql:dbname=Page_AuthDB;host=localhost;port=3306');
define('AUTH_DB_USER','youruser');
define('AUTH_DB_PASS','yourpass');

// Add your application database below,
// and in core/Auth.php:
// and in core/automation.php
// modify to connect to it after connecting to auth db


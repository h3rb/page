<?php

/************************************************************** Author: H. Elwood Gilliland III
 *  _____  _                 _____       _                    * Maintainer: B. Mackenzie
 * |  _  ||_| ___  ___  ___ |     | ___ | |_  ___  ___        * (c) 2015 PieceMaker Technologies
 * |   __|| || -_||  _|| -_|| | | || .'|| '_|| -_||  _|       * ---------------------------------
 * |__|__ |_||___||___||___||_|_|_||__,||_,_||____|_|         * Common settings for Aggregation
 * |_   _| ___  ___ | |_  ___  ___ | | ___  ___ |_| ___  ___  * service
 *   | |  | -_||  _||   ||   || . || || . || . || || -_||_ -| *
 *   |_|  |___||___||_|_||_|_||___||_||___||_  ||_||___||___| *
 *                                         |___|              *
 **************************************************************/

// Set the site's Auth DB here

define('AUTH_DB_DSN', 'mysql:dbname=Page_AuthDB;host=localhost;port=3306');
define('AUTH_DB_USER','youruser');
define('AUTH_DB_PASS','yourpass');

// Add your application database below,
// and in core/Auth.php:
// modify to connect to it after connecting to auth db


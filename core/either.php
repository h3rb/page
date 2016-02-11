<?php // Can be run as CLI or as a web page.  include 'core/either.php';
 if (php_sapi_name() == "cli") include 'core/automation.php';
 else include 'core/Page.php';

<?php

// Creates a special error handler that will put errors out to plog.  Works (sometimes)

// set to the user defined error handler
$old_error_handler = set_error_handler("myErrorHandler");

// error handler function
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
  $out='';
/*
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        echo 'out of scope';
        return;
    } */

    $error_type_name='Unknown Error';
    switch($errno) {
     case E_ERROR: $error_type_name='Fatal Error'; break;
     case E_WARNING: $error_type_name='Runtime Warning'; break;
     case E_PARSE: $error_type_name='Parse Error'; break;
     case E_NOTICE: $error_type_name='Notice'; break;
     case E_CORE_ERROR: $error_type_name='PHP Core Startup Error'; break;
     case E_CORE_WARNING: $error_type_name='PHP Core Warning'; break;
     case E_COMPILE_ERROR: $error_type_name='Zend Scripting Engine Error'; break;
     case E_COMPILE_WARNING: $error_type_name='Zend Scripting Engine Warning'; break;
     case E_USER_ERROR: $error_type_name='User Defined Error'; break;
     case E_USER_WARNING: $error_type_name='User Defined Warning'; break;
     case E_USER_NOTICE: $error_type_name='User Defined Notice'; break;
     case E_STRICT: $error_type_name='PHP Code Guideline'; break;
     case E_RECOVERABLE_ERROR: $error_type_name='Catchable Fatal Error'; break;
     case E_DEPRECATED: $error_type_name='Deprecation Runtime Notice'; break;
     case E_USER_DEPRECATED: $error_type_name='User Defined Deprecation Notice'; break;
     case E_ALL: $error_type_name='ALL ERRORS'; break;
    }

    $out.=$error_type_name.": [$errno] $errstr, $errfile line $errline";
    plog('ErrorHandler: '.$out);

    return false; //true;    /* Don't execute PHP internal error handler */
}

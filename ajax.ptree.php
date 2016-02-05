<?php // Ajax integration with Spacetree (JIT.js)

 include 'core/Page.php';

 if ( !Session::logged_in() ) exit;

 $getpost=page_input('json','T','F','I');
 if ( !$getpost ) exit;

 $ID=$getpost['I'];
 $Table=$getpost['T'];
 $Field=$getpost['F'];

 if ( !Auth::ACL('edit-'.$Table) && !Auth::ACL('edit-'.$Table.'-'.$Field) && !Auth::ACL('su') ) { echo '{"result":"readonly"}'; die; }

 if ( matches($Table,"OurTreeStructure") ) {
  global $database;
  $Model=new $Table($database);
  $Model->Set( $ID, array($Field=>$getpost['json']) );
 }

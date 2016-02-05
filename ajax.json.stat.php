<?php // Ajax intake for JSON reads directly from the database (just echo a certain field based on Table name)

 include 'core/Page.php';

 if ( Session::logged_in() ) {
  global $database;
  $getpost=getpost();
  if ( isset($getpost['I']) ) {
   $ID=$getpost['I'];
   if ( isset($getpost['T']) ) {
    $Table=$getpost['T'];
    $stats='';
// Just an example:
//    if ( matches($Table,'Item') ) {
//     $cc_model=new Item($database);
//     $cc=$cc_model->Get($ID);
//     if ( !false_or_null($cc) ) { // valid Item
//      echo $cc['Contents'];
//      exit;
//     }
    }
   }
  }
 }

echo '{"result":"error"}';

<?php // Ajax intake for HTML requests

 include 'core/Page.php';

 if ( Session::logged_in() ) {
  global $database;
  $getpost=getpost();
  if ( isset($getpost['I']) ) {
   $ID=$getpost['I'];
   if ( isset($getpost['T']) ) {
    $Table=$getpost['T'];
    $stats='';
//    if ( matches($Table,'Item') ) {
//     $i_model=new Item($database);
//     $item=$i_model->Get($ID);
//     if ( !false_or_null($item) ) { // valid Item
//      $stats='<div>'.$item['Name'].'</div>';
//      $stats='<div class="json-stat-item">'.$stats.'</div>'; //wrapper
//     } else $stats='No such item.';
//     echo $stats;
//    } else if ( matches($Table,'Part') ) { ...
   }
  }
 }

<?php // Ajax intake for JSON data requests

 include 'core/Page.php';

 if ( Session::logged_in() ) {
  global $database;
  $getpost=getpost();
  if ( isset($getpost['F']) ) {
   $Field=$getpost['F'];
   $parts=words($Field);
   if ( count(parts) > 1 ) $Field=$parts;
  }
  if ( isset($getpost['T'])
    && class_exists($getpost['T'])
    && matches(get_parent_class($getpost['T']),'Model') ) {
   $Table=$getpost['T'];
   $Model=new $Table($database);
   $result=array();
   if ( isset($getpost['L']) ) {
    $list=$getpost['L'];
    $list=ints($list);/////////////////
//    if ( matches($Table,'Item') ){
//     foreach ( $list as $e ) {
//      $e=$Model->Get($e);
//      $result[]=array(
//       0=>intval($e['ID']),
//       1=>$e['Name'],
//      );
//     }
//    } else if ( matches($Table,'Part') ){ ...
    } else foreach ( $list as $e ) { ////////////////
     $piece=array();
     $piece[]=$e;
     $r=$Model->Get($e);
     if ( !is_array($Field) ) $piece[]=$r[$Field]; else foreach ( $Field as $g ) $piece[]=$r[$g];
     $result[]=$piece;
    }
    echo json_encode($result);
    exit;
   }
  }
 }

 echo '{"result":"error"}';


// This kind of AJAX requires a high level of trust
// Use user permissions on writes, and per-user-group databasing to secure this
// Assume they have your data schema and are not being friendly


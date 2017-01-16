<?php


  include "core/Page.php";

  if ( !Session::logged_in() ) Page::Redirect("login");

//     data: { T:"'.$table.'", I:"'.$id.'", F:"'.$field.'", _i:i, _j:j, comment:c },

  $g=getpost();

  $table=$g["T"];

  if ( $table != "Earl" ) Page::Redirect("dash");

  $id=intval($g["I"]);
  $field=$g["F"];

  if ( !( $table == "Earl" && $field == "Comments" ) ) Page::Redirect("dash");

  $_i=intval($g["_i"]);
  $_j=intval($g["_j"]);
  $comment=$g["comment"];

  global $auth,$database;

  // check any access requirements here... none for now

  $model=new $table($database);
  $item=$model->Get($id);

  if ( false_or_null($item) || !isset($item[$field]) ) {  // couldn't find the thing we are commenting on
   Page::Redirect();
  }

 $data=$item[$field];

 if ( strlen(trim($data)) == 0 ) $data=array();
 else $data=json_decode($data,true);

  $node=array(
   "uid"=>$auth["ID"],
   "username"=>$auth["username"],   // not the best to store this for each comment, but saves us a round-trip-per-comment later...
   "comment"=>$comment,
   "time"=>strtotime("now"),
   "replies"=>array()
  );


 if ( $_i == 0 && $_j == 0 ) {  // Adds to main trunk
  $data[]=$node;
 } else {  // Adds to branch
  $start=&$data[($_i-1)]["replies"];
  while ( $j > 0 ) {
   $start=&$start["replies"];
   $j--;
  }
  $start[]=$node;
 }

 $model->Set($id,array(
  $field=>json_encode($data)
 ));

<?php // Table: Modification

 class Modification extends Model {

 public function byTableID($t,$id) {
  $results=array();
  $mods=$this->Select('What LIKE "%'.$t.'%" AND What LIKE "%'.$id.'%" ORDER BY Timestamp DESC');
  foreach ( $mods as $possible ) {
   $json=json_decode($possible['What'],true);
   foreach ( $json as $dataset ) {
    foreach ( $dataset as $table=>$change ) {
     if ( intval($change['I']) === intval($id) && matches($table,$t) ) $results[]=$possible;
    }
   }
  }
  return $results;
 }

 };

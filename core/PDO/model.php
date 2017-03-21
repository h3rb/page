<?php

 /*
  * Copyright (c) 2009
  * H. Elwood Gilliland III
  * New BSD License
  */

global $models;

class Reference {
 var
   $db,$ID,$source_table,$key,$data;
 public function __construct( &$db, $source_table, $key, $ID ) {
  $this->db=$db;
  $this->key=key;
  $this->ID=$ID;
 }
 public function Get() {
  $this->data=$this->db->Select( $ID );
  return $this->data;
 }
};

class Data {
 var $model,$db;
 var $_array,$_table,$reference,$referenced;
 public function __construct( $model, &$db, $_table, $in ) {
  $this->db=$db;
  $this->model=$model;
  $this->_table=$_table;
  $this->_array=$in;
  foreach ( $in as $key=>$value ) if ( stripos($key,"r_") === 0 ) {
   $this->references=new Reference( $db, $_table, $key, $value );
  } else {
   $this->{$key}=$value;
  }
  $this->referenced=array();
  $this->Construct();
 }
 public function Construct() {}
 public function References() {
  foreach ( $this->reference as $reference ) {
   $this->referenced[$reference->$key]=new Data($reference->Get());
  }
 }
};

class Model {
 var $db,$result,$table,$errors;

 public function __construct( &$db ) {
  $this->db=$db;
  $this->result=false;
  $this->table=$this->Table();
  $this->errors=array();
  $this->Construct();
  global $models; $models[$this->table]=&$this;
  plog("Model Instantiated: ".$this->table);
 }

 public function Construct() {}

 protected function Table() { return get_class($this); }

 public function Error() { return count($this->errors) > 0; }

 private function Errors( $err ) {
  plog('Model::Error '.$err);
  $this->errors[] = $err;
 }

 public function Last() {
  $top=$this->db->Select($this->table,'ID > 0','*','','ORDER BY ID DESC LIMIT 1');
  return $top[0]['ID'];
 }

/* can't have
 public function LastID() { return $this->db->LastID($this->table); }
 public function NextID() { return $this->db->NextID($this->table); }
*/

 public function Insert( $data ) {
  if ( !is_array($data) ) {
   $this->Errors("Insert could not proceed because data was not an array");
   return false;
  }
  $this->result=$this->db->Insert($this->table, $data);
  return $this->result;
 }

 public function Create( $data ) {
  if ( !is_array($data) )
  return $this->db->Insert($data);
  else
  return $this->db->Insert(array());
 }
 
 public function Join( $values, $tableA, $tableB, $order_by=FALSE, $columns='*', $limit=FALSE, $offset=0, $where=FALSE, $type='INNER', $on_or_using="ON" ) {
  return $this->db->Join($values,$tableA,$tableB,$order_by,$columns,$limit,$offset,$where,$type,$on_or_using); 
 } 

 public function Select( $where_clause=NULL, $fields="*", $prepared="", $order_by='', $limit='' ) {
  return $this->db->Select($this->table, $where_clause, $prepared, $fields, $order_by, $limit );
 }

 public function Range( $start, $limit=1000, $fields='*', $orderby='' ) {
  return $this->db->Run('SELECT '.$fields.' FROM '.$this->table.' '.$orderby.' LIMIT '.$start.','.$limit.';' );
 }

 public function Between( $test_field_numeric, $a, $b, $fields='*' ) { // Inclusive
  return $this->db->Run('SELECT '.$fields.' FROM '.$this->table
   .' WHERE ('.$test_field_numeric.' <= '.$a.' '.' AND '.$test_field_numeric.' >= '.$b.') '
   .' LIMIT '.$start.','.$limit.';'
  );
 }

 public function SelectBetween( $field="*", $prepared='', $order_by='', $limit='' ) {
  return $this->db->SelectBetween( $this->table, $field, $prepared, $order_by, $limit );
 }

 public function SelectWhereBetween( $where, $field="*", $prepared='', $order_by='', $limit='' ) {
  return $this->db->SelectWhereBetween( $where, $this->table, $field, $prepared, $order_by, $limit );
 }

 public function By( $field, $value, $order_by='', $prepared='' ) {
  $this->result = $this->Select( array( $field => $value ), "*", $prepared, $order_by );
  if ( false_or_null($this->result) ) $this->result=array();
  return $this->result;
 }

 public function First( $field, $value, $order_by='' ) {
  $this->result = $this->Select( array( $field => $value ), "*", '', $order_by.' LIMIT 1' );
  if ( false_or_null($this->result) ) return NULL;
  if ( is_array($this->result) && count($this->result) >= 1 ) return array_pop($this->result);
  return NULL;
 }

 public function All($order_by_limit='') {
  return $this->db->Run("select * from ".$this->table." ".$order_by_limit);
 }

 public function Get( $ID ) {
  $this->result = $this->Select( is_array($ID) ? $ID : array( 'ID' => $ID ) );
  if ( is_array($this->result) && count($this->result) == 1 )
   $this->result=array_pop($this->result);
  return $this->result;
 }

 public function GetData( $ID ) {
  $this->Get($ID);
  if ( false_or_null($this->result) || !is_array($this->result) ) return NULL;
  return new Data( $this, $this->db, $this->table, $this->result );
 }

 public function asObject( $in ) {
  if ( false_or_null($in) || !is_array($in) ) return NULL;
  return new Data( $this, $this->db, $this->table, $in );
 }

 public function Set( $ID, $data ) {
  return $this->Update($data, array('ID'=>$ID));
 }

 // Must Get() first
 public function Toggle( $ID, $key, $flag ) {
  if ( !false_or_null($this->result) ) {
   if ( isset($this->result[$key]) ) {
    $this->Set($ID,array($key=>bittoggle(intval($this->result[$key]),$flag)));
   }
  }
 }

 // Must Get() first
 public function On( $ID, $key, $flag ) {
  if ( !false_or_null($this->result) ) {
   if ( isset($this->result[$key]) ) {
    if ( bitoff($this->result[$key],$flag) )
     $this->Set($ID,array($key=>bittoggle(intval($this->result[$key]),$flag)));
   }
  }
 }

 // Must Get() first
 public function Off( $ID, $key, $flag ) {
  if ( !false_or_null($this->result) ) {
   if ( isset($this->result[$key]) ) {
    if ( biton($this->result[$key],$flag) )
     $this->Set($ID,array($key=>bittoggle(intval($this->result[$key]),$flag)));
   }
  }
 }


 public function Update( $data, $where_clause, $prepared="" ) {
  if ( !is_array($data) ) {
   $this->Errors("Update could not proceed because data was not an array");
   return false;
  }
  return $this->db->Update($this->table, $data, $where_clause, $prepared);
 }

 public function Delete( $where_clause, $prepared="" ) {
  return $this->db->Delete($this->table, $where_clause, $prepared);
 }

 public function Fields( $filter=false ) {
  return $this->db->Fields($this->table, $filter);
 }

 public function Duplicate( $ID, $prev_field=NULL ) {
  $source=$this->Get($ID);
  if ( false_or_null($source) ) return FALSE;
  if ( !false_or_null($prev_field) ) $source[$prev_field]=$ID;
  unset($source['ID']);
  return $this->Insert($source);
 }

 public function Execute( $query, $prepared="" ) { return $this->db->Run($query,$prepared); }

};

class Models {
 public function __construct( &$db, $as=FALSE ) {
  if ( $as !== FALSE ) $this->Load($db,$as);
 }
 public function Load( &$db, $as, $instantiate=TRUE ) {
  if ( is_string($as) ) {
   include_once SITE_ROOT.'/model/'.$as.'.php';
   if ( $instantiate === TRUE ) $this->{$as}=new $as($db);
  }
  else if ( is_array($as) ) {
   foreach ($as as $a) {
    include_once SITE_ROOT.'/model/'.$a.'.php';
    if ( $instantiate === TRUE ) $this->{$a}=new $a($db);
   }
  }
 }
};

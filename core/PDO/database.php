<?php

/*
 *  Copyright (c) 2009 H. Elwood Gilliland III
 *  New BSD License
 */

//define('TEST',1);

class Database extends PDO {
 var
  $errors, $query, $prepared, $result, $driver, $driver_code, $driver_codes;
 private $errorFunction;

 public function __construct($d, $u="", $p="",$driver='mysql') {
  $o = array(
   PDO::ATTR_PERSISTENT => true,
   PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  );
  try { parent::__construct($d, $u, $p, $o); }
  catch (PDOException $e) {
   plog('Database::Error in PDO/database.php');
   $this->errors[] = $e->getMessage();
   plog($e->getMessage());
   plog($d);
   plog($u);
   plog('(password hidden)');
   plog($driver);
   return null;
  }
  $result="";
  $this->errors=array();
  $this->driver_codes = array( 1=>'sqlite', 2=>'mysql' /* ... */ );
  if ( count($this->errors) == 0 ) {
   $this->driver = $driver; // didn't work: $this->getAttribute(PDO::ATTR_DRIVER_NAME);
   switch ( $this->driver ) {
    case 'sqlite': $this->driver_code = 1; break;
     case 'mysql': $this->driver_code = 2; break;
          default: $this->driver_code = 0; break;
   }
  } else {
   $this->driver='error';
   $this->driver_code=-1;
  }
 }

 // Debug messages
 private function Debug() {
  if(!empty($this->errorFunction)) {
   if(!empty($this->query)) $error["Query"] = $this->query;
   if(!empty($this->prepared))
   $error["Prepared"] = trim(print_r($this->prepared, true));
   $error["Backtrace"] = debug_backtrace();
   $this->errors[]=$error;
    $func = $this->errorCallbackFunction;
    $func($error);
  } else {
   global $plog_level;
   if ( $plog_level == 1 ) {
    plog('DB ERROR: '.vars($this->errors));
    $this->errors=array();
   }
  }
 }

 // Get desired fields from a table
 private function Fields($table, $filter=false) {
  switch ( $this->driver_code ) {
   case -1: return array( 'error'=>1 );
   case 1:
    $query = "PRAGMA table_info('" . $table . "');";
    $key = "name";
   break;
   case 2:
    $query = "DESCRIBE " . $table . ";";
    $key = "Field";
   break;
   default:
    $query = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $table . "';";
    $key = "column_name";
   break;
  }
  $this->result = $this->Run($query);
  if ( $this->result !== false ) {
   $fields = array();
   foreach($this->result as $record) $fields[] = $record[$key];
   if ( $filter !== false )
    return array_values(array_intersect($fields, array_keys($filter)));
   else return $fields;
  }
  return array();
 }

 // Converts a single word into an array for the special case of the prepared clause
 private function Clean($prepared) {
  if(!is_array($prepared)) {
   if(!empty($prepared)) $prepared = array($prepared);
   else $prepared = array();
  }
  return $prepared;
 }

 private function implode_fields( $data ) {
  return implode(", ",$data);
 }

 public function index_values( $values ) {
  $out=array();
  foreach ( $values as $k=>$v ) $out[]=$v;
  return $out;
 }

 private function implode_values( $values ) {
  return implode(", :",$values);
 }

 // Insert into $table using $data= array ( 'field'=>value )
 public function Insert($table, $data) {
  plog('db->Insert: table='.$table.', $data='.vars($data));
  $query = "INSERT INTO " . $table
     . " (" . $this->implode_fields($fields=array_keys($data))
     . ") VALUES (:" . $this->implode_values($fields) . ");";
  $prepared = array();
  foreach($fields as $field) $prepared[":$field"] = $data[$field];
  $this->result=$this->Run($query, $prepared);
  plog("Prepared: ".str_replace("\n","",vars($prepared)));
  return $this->lastInsertId();
 }

 public function Run($query, $prepared="") {
  plog("Query: ".$query);
  $this->query = trim($query);
  $this->prepared = $this->Clean($prepared);
  if ( defined('TEST') ) {
   return $this->query;
  }
  try {
   $pdo = $this->prepare($this->query);
   if($pdo->execute($this->prepared) !== false) {
    if(preg_match("/^(" . implode("|", array("select", "describe", "pragma")) . ") /i", $this->query))
     return $pdo->fetchAll(PDO::FETCH_ASSOC);
    elseif(preg_match("/^(" . implode("|", array("delete", "insert", "update")) . ") /i", $this->query))
     return $pdo->rowCount();
   }
  } catch (PDOException $e) {
   $this->errors[] = $e->getMessage();
   $this->Debug();
   return false;
  }
 }

 public function Where( $array ) {
  if ( count($array) === 0 ) return '';
  $clause=' WHERE ';
  $i=0;
  foreach ( $array as $k=>$v ) {
   $clause.=($i!=0 ? ' AND ' : '') . $k . '=' . $this->quote($v);
   $i++;
  }
  return $clause;
 }

 public function SelectWhereBetween($where, $table, $field, $low, $high, $order_by='', $limit='' ) {
  return $this->Select(
    $table,
    (' ( ('.$field.' >= '.$low.') AND ('.$field.' <= '.$high.') AND ('.$where.') ) ' ),
    '','*',$order_by,$limit);
 }

 public function SelectBetween($table, $field, $low, $high, $order_by='', $limit='' ) {
  return $this->Select(
    $table,
    (' ( ('.$field.' >= '.$low.') AND ('.$field.' <= '.$high.') ) ' ),
    '','*',$order_by,$limit);
 }

 public function Select($table, $where_clause="", $prepared="", $fields="*", $order_by='', $limit='') {
  $query = "SELECT " . $fields . " FROM " . $table;
  if ( is_array($where_clause) ) $query.=Database::Where($where_clause);
  else if(!empty($where_clause)) $query .= " WHERE " . $where_clause;
  $query .= (strlen($order_by)>0?(' ' . $order_by):'');
  if ( strlen(trim($limit)) > 0 ) $query .= ' LIMIT '.$limit;
  $query .= ";";
  $this->result = $this->Run($query, $prepared);
  return $this->result;
 }
 public function SelectOR($table, $where_clause="", $prepared="", $fields="*") {
  $query = "SELECT " . $fields . " FROM " . $table;
  if ( is_array($where_clause) ) $query.=Database::Where($where_clause);
  else if(!empty($where_clause)) $query .= " WHERE " . $where_clause;
  $query .= ";";
  $this->result = $this->Run($query, $prepared);
  return $this->result;
 }
 
  public function Join( $values, $tableA, $tableB, $order_by=FALSE, $columns='*', $limit=FALSE, $offset=0, $where=FALSE, $type='INNER', $on_or_using="ON" ) {
  if ( is_array($tableA) ) $tableA=implode(',',$tableA);
  if ( is_array($tableB) ) $tableB=implode(',',$tableB);
  if ( is_array($values) ) {
   $value=array();
   foreach ( $values as $a=>$b ) {
    $value[]=$a.'='.$b;
   }
   $value=implode(',',$value);
  } else $value=$values;
  $ending='';
  if ( !false_or_null($where) ) {
   if ( is_array($where) ) $ending.=$this->where;
   else $ending.=$where;
  }  
  if ( !false_or_null($order_by) && strlen($order_by)>0 ) {
   $ending.=' ORDER BY '.$order_by;
  }
  if ( !false_or_null($limit) ) {
   $limit=intval($limit);
   $ending.=' LIMIT '.$limit;
   if ( $offset > 0 ) $ending.=' OFFSET '.$offset;
  }
  $query='SELECT '.$columns.' FROM '.$tableA.' '.$type.' JOIN '.$tableB.' '.$on_or_using.' '.$value.$ending.';';
  $result=$this->Run($query);
  if ( false_or_null($result) ) return array();
  if ( count($result) == 1 ) return array_shift($result);
  return $result;
 }

 public function RunCountQuery($query, $prepared="") {
  plog("Query (counter): ".$query);
  $this->query = trim($query);
  $this->prepared = $this->Clean($prepared);
  if ( defined('TEST') ) {
   return $this->query;
  }
  try {
   $pdo = $this->prepare($this->query);
   if($pdo->execute($this->prepared) !== false) {
    $count=$result->fetch(PDO::FETCH_NUM);
    return $count[0];
   }
  } catch (PDOException $e) {
   $this->errors[] = $e->getMessage();
   $this->Debug();
   return false;
  }
 }
 public function Count($table, $where_clause="", $prepared="", $fields="count(*)" ) {
  $query = "SELECT " . $fields . " FROM " . $table;
  if ( is_array($where_clause) ) $query.=Database::Where($where_clause);
  else if(!empty($where_clause)) $query .= " WHERE " . $where_clause;
  $query .= ";";
  $this->result = $this->RunCountQuery($query, $prepared);
  return $this->result[0];
 }

 public function setErrorCallback($Function) {
  if ( in_array(strtolower($Function), array("echo", "print")) ) $Function = "print_r";
  if ( function_exists($Function) ) $this->errorFunction = $Function;
 }

 public function Update($table, $data, $where_clause, $prepared="") {
  $fields = array_keys($data);
  $size = count($fields);
  $query = "UPDATE " . $table . " SET ";
  for ( $f = 0; $f < $size; ++$f) {
   if($f > 0) $query .= ", ";
   $query .= $fields[$f] . " = :update_" . $fields[$f];
  }
  if ( is_array($where_clause) ) $query.=Database::Where($where_clause);
  else if(!empty($where_clause)) $query .= " WHERE " . $where_clause;
  $prepared = $this->Clean($prepared);
  foreach ($fields as $field) $prepared[":update_$field"] = $data[$field];
  $this->result = $this->Run($query, $prepared);
  plog("Prepared: ".str_replace("\n","",vars($prepared)));
  return $this->result;
 }

 // Delete from a table using a where clause
 public function Delete($table, $where_clause, $prepared="") {
  if ( !is_array($where_clause) ) { plog("HEY YOU DIDN'T MEAN TO DELETE EVERYTHING, RIGHT? (->Delete() requires array())"); return FALSE; }
  $query = "DELETE FROM " . $table;
  if ( is_array($where_clause) ) $query.=Database::Where($where_clause);
  else if(!empty($where_clause)) $query .= " WHERE " . $where_clause;
  $this->result = $this->Run($query, $prepared);
  return $this->result;
 }

  public function last_query_count() {
   return $this->query("SELECT FOUND_ROWS()")->fetchColumn();
  }

}


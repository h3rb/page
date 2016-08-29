<?php

/* Gets information from Linux / ARM Hamachi command output and converts it to a PHP class */

 class HamachiNode {
  var $connected,$client_id,$nick,$ip,$alias,$ipv6,$type,$protocol,$origination_ip,$origination_port;
  public function __construct( $line ) {
   $line=explode(" ",str_replace("not set","not-set",$line));
   $line=dropzerolen($line);
   if ( stripos($line[0],"*") === 0 ) {
    $this->connected = TRUE;
    $this->client_id = $line[1];
    $this->nick = $line[2];
    $this->ip = $line[3];
    $this->alias = $line[5];
    $this->ipv6 = $line[6];
    $this->type = $line[7];
    $this->protocol = $line[8];
    $origination = explode(":",$line[9]);
    $this->origination_ip = $origination[0];
    $this->origination_port = $origination[1];
   } else {
    $this->connected = FALSE;
    $this->client_id = $line[0];
    $this->nick = $line[1];
    $this->ip = $line[2];
    $this->alias = $line[4];
   }
  }
  public function CSV() {
   return 'NODE,' . ($this->connected ? 1 : 0)
         . ',' . $this->client_id
         . ',' . $this->nick
         . ',' . $this->ip
         . ',' . $this->alias
         . ',' . $this->ipv6
         . ',' . $this->type
         . ',' . $this->protocol
         . ',' . $this->origination_ip
         . ',' . $this->origination_port;
  }
 };

 class HamachiNetwork {
  var $name,$capacity,$type,$owner,$connected;
  var $list;
  public function __construct( $line ) {
   $line=explode(" ",$line);
   $line=dropzerolen($line);
   if ( stripos($line[0],"*") === 0 ) {
    $this->name=str_replace(array("[","]"),'',$line[1]);
    $capacity = explode("/",$line[3]);
    $this->capacity=str_replace(",",'',$capacity[1]);
    $this->type=str_replace(",",'',$line[6]);
    $this->owner=$line[8];
   } else {
    $this->name=str_replace(array("[","]"),'',$line[0]);
    $this->connected=FALSE;
   }
   $this->list=array();
  }
  public function Add($line) {
   $node = new HamachiNode($line);
   $this->list[] = &$node;
   return $node;
  }
  public function CSV() {
   $out= 'NETWORK,' . $this->name
       . ',' . $this->capacity
       . ',' . $this->type
       . ',' . $this->owner
       . ',' . count($this->list) . "\n";
   foreach ( $this->list as $node ) $out.=$node->CSV()."\n";
   return $out;
  }
  public function Find($name) {
   foreach ( $this->list as $node ) {
    if ( contains($node->nick,$name) || matches($node->nick,$name) ) return $network;
   }
   return FALSE;
  }
 };

 class Hamachi {

  var $client_id,$status,$ip,$ipv6,$version,$pid,$nickname,$lmi;
  var $list;
  var $security;

  public function Status() {
   $out = 'SELF,'.$this->client_id
       . ',' . $this->status
       . ',' . $this->ip
       . ',' . $this->ipv6
       . ',' . $this->version
       . ',' . $this->pid
       . ',' . $this->nickname
       . ',' . $this->lmi
       . ',' . ($this->security ? 1 : 0);
   foreach ( $this->list as $network ) {
    $out.=$network->CSV();
   }
   return $out;
  }

  public function __construct() {
   $this->list=array();
   $this->security = contains(file_get_contents('/offline/hamachi.login.txt'),"not have permission");
   $h = explode("\n",file_get_contents('/offline/hamachi.txt'));
   $l = explode("\n",file_get_contents('/offline/hamachi.list.txt'));
   $network = NULL;
   if ( !$this->security ) {
    foreach ( $l as $line ) {
     if ( strpos($line," * [") === 0 ) { // active network
      $network = new HamachiNetwork($line);
      $this->list[] = &$network;
     } else if ( !false_or_null($network) && strlen(trim($line)) > 0 ) {
      $network->Add($line);
     }
    }
    foreach ( $h as &$k ) if ( strlen(trim($k)) > 0 ) {
     $k=explode(": ",$k);
     $k=trim($k[1]);
    }
    $this->version=$h[0];
    $this->pid=$h[1];
    $this->status=$h[2];
    $this->client_id=$h[3];
    $address=explode(" ",$h[4]);
    $this->ip=$address[0];
    $this->ipv6=$address[1];
    $this->nickname=$h[5];
    $this->lmi =$h[6];
   }
  }

  public function Find($name) {
   foreach ( $this->list as $network ) {
    if ( contains($network->name,$name) || matches($network->name,$name) ) return $network;
   }
   return FALSE;
  }

 };

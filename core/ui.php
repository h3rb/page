<?php

 include_once 'unique.php';

 class UI extends Unique {
  var $db,$head_js,$inline_js,$css,$html,$js_data,$local_unique;
  public function __construct( &$db, $parameters=FALSE ) {
   $this->Uniqueness();
   $this->db=$db;
   $this->_Init($parameters);
   $this->local_unique=0;
  }
  public function _Reset() {
   $this->head_js="";
   $this->inline_js="";
   $this->css="";
   $this->html="";
   $this->js_data=array();
   $this->Reset();
  }
  public function _Init( $parameters ) { $this->_Reset(); $this->Init($parameters); }
  public function _Implement() { $this->_Reset();  $this->Implement(); }
  public function UID() { return 'ui'.$this->id; }
  public function N($postfix) { return $this->UID().$postfix; }
  public function _GetPreloaded() {
   $out="";
   $first=0;
   foreach ( $this->js_data as $value ) {
    $name=$this->N('_'.$first);
    $json = json_encode($value);
    $out.=' var '.$name.'; '.$name.'='.$json.';'."\n";
    $first++;
   }
   return $out;
  }
  public function Preload( $value ) {
   if ( is_array($value) ) { // Incoming from a key-value array
    $first=count($this->js_data);
    foreach ( $value as $k=>$v ) {
     $this->js_data[]=$v;
    }
    return $first;
   }
   $this->js_data[]=$value;
   return $this->N('_'.(count($this->js_data)-1));
  }
  public function CSS( $name, $style ) {
   $name=str_split(trim($name));
   $type=''.array_shift($name);
   $name=implode('',$name);
   $name=$type.$this->N('-'.$name);
   $css = $name . ' {'.$style.'}'."\n";
   $this->css .= $css . "\n";
   return $name;
  }
  public function JSTemplate( $name, $replacements=FALSE, $head=FALSE ) {
   $fn=SITE_ROOT.'/ui/'.$name;
   if ( isfile($fn) ) {
    $result=file_get_contents($fn);
    if ( is_array($replacements) ) foreach ( $replacements as $k=>$v ) {
     $value=str_replace("#@",''.$this->local_unique,$this->UID().'_'.$v);
     $result=str_replace($k,$value,$result);
    }
    if ( $head !== FALSE ) $this->head_js.=$result;
    else $this->inline_js.=$result;
    return $result;
   } else {
    plog('ui->JSTemplate('.$name.')','file not found');
    return "";
   }
  }
  protected function Reset() {}
  protected function Init( $parameters ) {}
  protected function Implement() {}
 };

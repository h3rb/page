<?php

 // Manages CSS settings on a DOM element.

 class CSS {
  var $tag;
  var $classes;
  var $styling;
  public function __construct() {
   $this->tag=array();
   $this->classes=array();
   $this->styling=array();
  }
  public function Duplicate() {
   $css=new CSS;
   $css->tag=array_copy($this->tag);
   $css->classes=array_copy($this->classes);
   $css->styling=array_copy($this->styling);
   return $css;
  }
  public function AddClass($c) { $this->classes[]=$c; }
  public function Style( $a, $b ) { $this->styling[$a]=$b; }
  public function GetStyles() {
   $out='';
   foreach ( $this->styling as $property=>$definition )
    $out.=$property.':'.$definition.';';
   return $out;
  }
  public function GetClasses() {
   return implode(" ",$this->classes);
  }
  public function BorderLeft( $color="black", $style="solid", $width="1px" ) {
   $this->Style( "border-left", "$width $style $color" );
  }
  public function BorderRight( $color="black", $style="solid", $width="1px" ) {
   $this->Style( "border-right", "$width $style $color" );
  }
  public function BorderTop( $color="black", $style="solid", $width="1px" ) {
   $this->Style( "border-top", "$width $style $color" );
  }
  public function BorderBottom( $color="black", $style="solid", $width="1px" ) {
   $this->Style( "border-bottom", "$width $style $color" );
  }
  public function Border( $color="black", $style="solid", $width="1px" ) {
   $this->BorderLeft($color,$style,$width);
   $this->BorderRight($color,$style,$width);
   $this->BorderTop($color,$style,$width);
   $this->BorderBottom($color,$style,$width);
  }
  public function BGTransparent() {
   $this->Style("background-color","transparent");
  }
  public function BGImage( $file ) {
   $this->Style("background","../i/$file");
  }
  public function BGColor( $color ) {
   $this->Style("background-color",$color);
  }
  public function BGRepeat( $value ) {
   $this->Style("background-repeat",$value);
  }
  public function BGClip( $values ) {
   $this->Style("background-clip",$values);
  }
  public function BGPosition( $values ) {
   $this->Style("background-position",$values);
  }

  public function Display( $value ) {
   $this->Style("display",$value);
  }

  public function GetTag($tagname,$tagarray=NULL,$interior=FALSE,$closetag=FALSE ) {
   if ( !is_array($tagarray) ) $tagarray=array();
   $tagarray=array_merge($this->tag,$tagarray);
   $tagarray['class'] = ( isset($tagarray['class']) ? $tagarray['class'] : '' ) . $this->GetClasses();
   if ( strlen(trim($tagarray['class'])) === 0 ) unset($tagarray['class']);
   $tagarray['style'] = ( isset($tagarray['style']) ? $tagarray['style'] : '' ) . $this->GetStyles();
   if ( strlen(trim($tagarray['style'])) === 0 ) unset($tagarray['style']);
   $tag='<'.$tagname;
   foreach ( $tagarray as $e=>$v ) {
    if ( is_bool($v) ) $tag.=' '.$e;
    else $tag.=' '.$e.'="'.$v.'"';
   }
   $tag.='>';
   if ( $interior !== FALSE ) $tag.=$interior;
   if ( $closetag !== FALSE ) $tag.='</'.$tagname.'>';
   return $tag;
  }
 };

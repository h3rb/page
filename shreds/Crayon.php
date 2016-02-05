<?php

 // Copyright (c) 2014 Herb Gilliland @ Lost Astronaut Studios
 // License: BSD
 // Origin: lostastronaut.com

 class Crayon {
  var $r,$g,$b,$a; // Always floating point normalized 0-1
  public function __construct() {
   $this->r=0.0;
   $this->g=0.0;
   $this->b=0.0;
   $this->a=1.0;
   $arg=func_get_args();
   if ( count($arg) == 3 ) $this->SetRGB($arg[0],$arg[1],$arg[2]);
   if ( count($arg) == 4 ) $this->SetRGBA($arg[0],$arg[1],$arg[2],$arg[3]);
  }
  public function Duplicate() {
   $c= new Crayon;
   $c->r= $this->r;
   $c->g= $this->g;
   $c->b= $this->b;
   $c->a= $this->a;
   return $c;
  }
  public function Set( $r,$g=NULL,$b=NULL ) {
   if ( is_object($r) && matches(get_class($r),'Crayon') ) {
    $this->r=$r->r;
    $this->g=$r->g;
    $this->b=$r->b;
   } else {
    $this->r=floatval($r);
    $this->g=floatval($g);
    $this->b=floatval($b);
   }
  }
  public function SetRGB( $r, $g, $b ) {
   $this->r=floatval($r);
   $this->g=floatval($g);
   $this->b=floatval($b);
  }
  public function SetRGBA( $r, $g, $b, $a ) {
   $this->r=floatval($r);
   $this->g=floatval($g);
   $this->b=floatval($b);
   $this->a=floatval($a);
  }
  public function RGB() {
   return 'rgba('.intval($this->r*255.0).','.intval($this->g*255.0).','.intval($this->b*255.0).')';
  }
  public function RGBA() {
   if ( $this->a === 1 ) return $this->RGB();
   return 'rgba('.intval($this->r*255.0).','.intval($this->g*255.0).','.intval($this->b*255.0).','.$this->a.')';
  }
  public function Additive( $r, $g=NULL, $b=NULL ) {
   if ( is_object($r) && matches(get_class($r),'Crayon') ) {
    $this->r+=$r->r;
    $this->g+=$r->g;
    $this->b+=$r->b;
   } else {
    $this->r+=floatval($r);
    $this->g+=floatval($g);
    $this->b+=floatval($b);
   }
   $this->Absolute();
  }
  public function Multiply( $r, $g=NULL, $b=NULL ) {
   if ( is_object($r) && matches(get_class($r),'Crayon') ) {
    $this->r*=$r->r;
    $this->g*=$r->g;
    $this->b*=$r->b;
   } else {
    $this->r*=floatval($r);
    $this->g*=floatval($g);
    $this->b*=floatval($b);
   }
   $this->Absolute();
  }
  public function Subtract( $r, $g=NULL, $b=NULL ) {
   if ( is_object($r) && matches(get_class($r),'Crayon') ) {
    $this->r-=$r->r;
    $this->g-=$r->g;
    $this->b-=$r->b;
   } else {
    $this->r-=floatval($r);
    $this->g-=floatval($g);
    $this->b-=floatval($b);
   }
   $this->Absolute();
  }
  public function Absolute() {
   $this->r=abs($this->r);
   $this->g=abs($this->g);
   $this->b=abs($this->b);
   $this->a=abs($this->a);
  }
  public function Blend( $blending, $b ) {
   switch ( $blending ) {
    case '=': $this->Set($b); break;
    case '+': $this->Additive($b); break;
    case '-': $this->Subtract($b); break;
    case '*': $this->Multiply($b); break;
   }
  }
 };

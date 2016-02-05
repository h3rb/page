<?php // Root class

 include_once 'utility.php';

 class Root {
  public function of( $match ) {
   return is($match,get_class($this)) ? TRUE : FALSE;
  }
  public function is_of( $parent ) {
   if ( is_object( $parent ) ) { if ( is_subclass_of( $this, get_class($parent) ) ) return TRUE; }
   else if ( is_string( $parent ) ) { return is_subclass_of( $this, $parent ) ? TRUE : FALSE; }
   else if ( is_array( $parent ) ) { // Multiple parents provided
    foreach ( $parent as $classname ) {
     if ( is_object($classname) ) { if ( is_subclass_of( $this, get_class($classname) ) ) return TRUE; }
     else if ( is_array($classname) ) { if ( $this->is_of($classname) ) return TRUE; }
     else if ( is_string($classname) ) { if ( is_subclass_of( $this, $classname ) ) return TRUE; }
     else error(get_class($this).':isof(`'.$classname.'`)','Invalid parent classname in array as non-object, non-string, non-array');
    }
   }
   return FALSE;
  }
 };

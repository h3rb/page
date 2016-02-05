<?php

 // Keeps track of a unique identifier when using the ui.php functionality.
 //  Turns deployed UI javascript stuff into singletons.

 global $unique;
 $unique=array();

 class Unique {
  var $id;
  public function Uniqueness() {
   global $unique;
   $classname=get_class($this);
   if ( !isset($unique[$classname]) ) $unique[$classname]=intval(getorpost('ajax')+getorpost('u'));
   else $unique[$classname]++;
   $this->id=$unique[get_class($this)];
   return $this->id;
  }
 };

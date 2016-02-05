<?php

 abstract class AJAX {
  public function fp( $a ) { if(contains($a,'.')) {$b=explode('.',$a);return array($b[0]=>$b[1]);} else return $a; }
  public function FormPost() {
   $out=array();
   $out["map"]=array();
   foreach ( getpost() as $name=>$value ) {
    if ( matches($name,"map") ) {
     $map=json_decode(FormHelper::Decode($value));
     foreach ( $map as $mapped ) {
      $parts=explode('|',$mapped);
      if ( contains($parts[0],'__') ) {
       $d=explode('__',$parts[0]);
       if ( !isset($out["map"][$d[0]]) ) $out["map"][$d[0]]=array();
       $out["map"][$d[0]][$d[1]]=AJAX::fp($parts[1]);
      } else {
       $mapped[$parts[0]]=AJAX::fp($parts[1]);
      }
     }
    } else if ( contains($name,'__') ) {
     $d=explode("__",$name);
     if ( !isset($out[$d[0]]) ) $out[$d[0]]=array();
     $out[$d[0]][$d[1]]=base64_decode(urldecode($value));
    } else
    $out[$name]=FormHelper::Decode($value);
   }
   $out['signal']=json_decode($out['signal'],true);
   return $out;
  }
  public function Field( &$FormPost, $form, $eleName ) {
   $map=&$FormPost['map'];
   foreach ( $map as $f=>$elements ) {
    if ( matches($f,$form) ) {
     foreach ( $elements as $named=>$e ) {
      if ( matches($eleName,$named) ) {
       return $e;
      }
     }
     break;
    }
   }
   return FALSE;
  }
  public function Element( &$FormPost, $form, $a,$b=FALSE ) {
   $map=&$FormPost['map'];
   if ( count($c=explode('.',$a)) > 1 ) $b=$c[1];
   foreach ( $map as $f=>$elements ) {
    if ( matches($f,$form) ) {
     foreach ( $elements as $named=>$e ) {
      if ( is_array($e) ) {
       if ( isset($e[$a]) && matches($e[$a],$b) ) return $named;
      } else if ( matches($e,$a) && $b===FALSE ) return $named;
     }
     break;
    }
   }
   return FALSE;
  }
  public function Value( &$FormPost, $form, $a,$b=FALSE ) {
   $map=&$FormPost['map'];
   if ( count($c=explode('.',$a)) > 1 ) $b=$c[1];
   foreach ( $map as $f=>$elements ) {
    if ( matches($f,$form) ) {
     foreach ( $elements as $named=>$e ) {
      if ( is_array($e) ) {
       if ( isset($e[$a]) && matches($e[$a],$b) ) return $FormPost[$form][$named];
      } else if ( matches($e,$a) && $b===FALSE ) return $FormPost[$form][$named];
     }
     break;
    }
   }
   return FALSE;
  }
  public function Values( &$FormPost, $form ) {
   $out=array();
   $map=&$FormPost['map'];
   if (  count($c=explode('.',$a)) > 1 ) $b=$c[1];
   foreach( $map as $f=>$elements ) {
    if ( matches($f,$form) ) {
     foreach ( $elements as $named=>$e ) {
      if ( is_array($e) ) {
       foreach ( $e as $n=>$v ) {
        $out[$n][$v]=$FormPost[$form][$named];
       }
      } else $out[$e]=$FormPost[$form][$named];
     }
    }
   }
   return $out;
  }
 };

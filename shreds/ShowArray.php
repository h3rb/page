<?php

 function first_is_array( $arrarr ) {
  $temp=array_shift($arrarr);
  return is_array($temp);
 }

 function has_namekey( $arr ) {
  foreach ( $arr as $keyed=>$ele ) if ( !is_numeric($keyed) ) return true;
  return false;
 }

 function get_show_array( $arr ) {
  $html='';
  foreach ( $arr as $named=>$ele ) {
   if ( is_string($ele) ) {
    $html.='<table width="100%" cellpadding=0 cellspacing=0><tr><td width="25%"><b>'.$named.'</b></td><td>'.$ele.'</td><tr></table>';
   } else if ( is_array($ele) ) {
    $html.='<h4>'.$named.'</h4>';
    $html.='<table width="100%"><tr>';
    if ( first_is_array($ele) ) {
     if ( !has_namekey($ele) ) {
      $html.='<table width="100%">';
      foreach ( $ele as $n=>$e ) {
       $html.='<tr>';
        foreach ( $e as $a ) {
         $html.='<td>'.$a.'</td>';
        }
       $html.='</tr>';
      }
      $html.='</table>';
     } else {
      $html.='<table width="100%">';
      foreach ( $ele as $n=>$e ) {
        foreach ( $e as $n=>$a ) {
         $html.='<tr>';
         $html.='<td>'.$n.'</td>';
         $html.='<td>'.$a.'</td>';
         $html.='</tr>';
        }
      }
      $html.='</table>';
     }
    } else {
     foreach ( $ele as $n=>$e ) {
      $html.='<span class="hilite">'.$e.'</span>&nbsp; ';
     }
    }
   } else {
    $html.='<table width="100%"><td width="25%"><b>'.$named.'</b></td><td>'.$ele.'</td><tr></table>';
   }
  }
  return $html;
 }

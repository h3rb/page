<?php

 function linkbar( $arr ) {

  $out='<div id="linkbar">';
  foreach ( $arr as $label=>$uri ) {
   $out.='<span class="buttonlink">'.$label.'</span>';
  }
  $out='</div>';
  return $out;
 }

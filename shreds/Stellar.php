<?php

 global $_stellars;
 $_stellars=0;

 function Stellar( &$p, $layers, $w=190, $h=108, $return_html=FALSE ) {
  global $_stellars;
  $_stellars++;
  if ( $_stellars === 1 ) {
   $p->JS('jquery.stellar.min.js');
  }
  $id="stellar_".$_stellars;
  $html='<div id="'.$id.'" style="width:'.$w.'px; height:'.$h.'px;">';
  foreach ( $layers as $layer ) {
   $html.='<div data-stellar-ratio="'.$layer['ratio'].'"><img src="'.$layer['img'].'" width="'.$w.'" height="'.$h.'"></div>';
  }
  $html.='</div>';
  $p->JQ('
   $("#'.$id.'").stellar({
    responsive:true
   });
  ');
  if ( $return_html === FALSE ) { $p->HTML($html); return ''; }
  else return $html;
 }


 function ImagesLayered( $layers, $w=190, $h=108, $return_html=FALSE, $z_index=1000 ) {
  $html='<div style="position: relative; left: 0; top: 0; width:'.$w.'px; height:'.$h.'px; margin:0; padding:0;">';
  $i=$z_index;
  foreach ( $layers as $layer ) {
   $i++;
   $html.='<img src="'.$layer['img'].'" style="z-index: '.$i.'; position: absolute; top: 0; left: 0; height:'.$h.'px; margin:0; padding:0;"/>';
  }
  $html.='</div>';
  return $html;
 }

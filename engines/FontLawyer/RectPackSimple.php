<?php

define( 'fl_jpeg_biggest_dim', 65000 );

// Just makes a long ticker-tape.

function PackRect(
  $rects,
  $_lo_w=64, $_lo_h=64,
  $_max_w=2048, $_max_h=2048,
  $_delta_w=10, $_delta_h=10
 ) {
 $results=array();
 $i=0;
 $cx=0;
 $cy=0;
 $biggest_y=0;
 $totalh=0;
 foreach ( $rects as $rect ) {
  $results[$i]=$rect;
  $w=intval($rect['w']);
  if ( $cx+$w > fl_jpeg_biggest_dim ) {
   $cx=0;
   $cy+=$biggest_y;
  }
  $h=intval($rect['h']);
  if ( $biggest_y < $h ) {
   $biggest_y=$h;
  }
  $results[$i]['x']=$cx;
  $results[$i]['y']=$cy;
  $cx+=$w;
  $i++;
 }
 $results['width']=$cx;
 $results['height']=$cy+$biggest_y;
 $results['valid']=true;
 return $results;
}

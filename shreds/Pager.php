<?php

 // Performs super-simple pagination for tables
 if ( !function_exists('Pager') ) {
 function Pager( &$p, $url, $start, $limit, $total ) {
  $prev=$start-$limit;
  if ( $prev < 0 ) $prev=0;
  $next=$start+$limit;
  //var_dump($total);
  $pages=$total/$limit;
  if ( $total % $limit > 0 ) $pages+=1;
  $pages_before=$start/$limit;
  $pages_after=$total/$limit-$pages_before-1;
  $html='<div class="pager">';
  if ( $start > 0 )
  $html.='<a class="roundbutton" href="'.$url.'&start='.$prev.'" alt="Prev" title="Previous '.$limit.'"><span class="fi-previous"></span></a>';
  if ( $pages_before+$pages_after+1 > 30 ) {
   if ( $pages_before > 10 ) {
    $html.='...';
    $pages_before=10;
   }
   for ( $i=0; $i<$pages_before; $i++ )
    $html.='<a href="'.$url.'&start='.($limit*$i).'" title="Page '.($i+1).'"><span class="fi-die-one gray"></span></a>';
   $html.='<a href="#" title="Current Page: '.($pages_before+1).' of '.$pages.'"><span class="fi-die-one orangy"></span></a>';
   if ( $pages_after > 10 ) {
    $pages_after=10;
    for ( $i=0; $i<$pages_after; $i++ )
     $html.='<a href="'.$url.'&start='.($start+$limit*($i+1)).'" title="Page '.($pages_before+1+$i+1).'"><span class="fi-die-one gray"></span></a>';
    $html.='...';
   } else
   for ( $i=0; $i<$pages_after; $i++ )
    $html.='<a href="'.$url.'&start='.($start+$limit*($i+1)).'" title="Page '.($pages_before+1+$i+1).'"><span class="fi-die-one gray"></span></a>';
  } else {
   for ( $i=0; $i<$pages_before; $i++ )
    $html.='<a href="'.$url.'&start='.($limit*$i).'" title="Page '.($i+1).'"><span class="fi-die-one gray"></span></a>';
   $html.='<a href="#" title="Current Page: '.($pages_before+1).' of '.$pages.'"><span class="fi-die-one orangy"></span></a>';
   for ( $i=0; $i<$pages_after; $i++ )
    $html.='<a href="'.$url.'&start='.($start+$limit*($i+1)).'" title="Page '.($pages_before+1+$i+1).'"><span class="fi-die-one gray"></span></a>';
  }
  if ( $total < $limit ) {
   $html.='<a href="#" title="This is the only page."><span class="fi-die-one"></span></a>';
  }
  if ( $next < $total )
  $html.=' <a class="roundbutton" href="'.$url.'&start='.$next.'" alt="Next" title="Next '.$limit.'"><span class="fi-next"></span></a>';
  $html.='&nbsp; Showing '.($start+1).' to '.($total<$start+$limit?$total:$start+$limit).' of '.$total;
  $html.='</div>';
  $p->HTML( $html );
 }
 }

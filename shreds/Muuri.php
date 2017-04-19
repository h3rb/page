<?php

 global $_MuuriStarted; $_MuuriStarted=0;

 function Muuri_Start( &$p, $container="muuri_grid" ) {
  global $_MuuriStarted;
  if ( $_MuuriStarted === 0 ) {
   $p->JQuery();
   $p->CSS('muuri.css');
   $p->JS('velocity.min.js');
   $p->JS('hammer.min.js');
  }
  $_MuuriStarted++;
  $p->HTML('<div class="'.$container.'" id="'.$container.$_MuuriStarted.'"> <!--START: Muuri-->');
 }

 function Muuri_Item( &$p, $content, $class="muuri_item", $content_wrapper="custom-content" ) {
  $p->HTML('<div class="'.$class.'">');
   $p->HTML('<div class="muuri_item-content"><div class="'.$content_wrapper.'">');
     $p->HTML($content);
    $p->HTML('</div>');
   $p->HTML('</div>');
  $p->HTML('</div>');
 }

 function Muuri_Item_Start( &$p, $clas="muuri_item", $content="custom-content" ) {
  $p->HTML('<div class="'.$clas.'"><div class="muuri_item-content"><div class="'.$content.'">');
 }

 function Muuri_Item_End( &$p ) {
  $p->HTML('</div></div>');
 }

 function Muuri_End( &$p, $container="muuri_grid", $item="muuri_item" ) {
  global $_MuuriStarted;
  $p->HTML(' </div> <!--END: Muuri--> ');
  if ( $_MuuriStarted === 1 ) {
   $p->HTML('<script src="js/muuri.min.js"></script>');
  }
  $p->HTML('
  <script>
   var muuri_grid'.$_MuuriStarted.' = new Muuri({
    containerClass: "'.$container.'",
//    layout: [ "firstFit", {fillGaps:true} ],
    container: document.getElementById("'.$container.$_MuuriStarted.'"),
    items: [].slice.call(document.getElementsByClassName("'.$item.'"))
   });
  </script>
  ');
 }

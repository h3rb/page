<?php

 /*

  Of the form:

    array(
     "Menu Item 1"=>array( 0=>"Submenu Item 1|link",

 */

 function Dropdown_link( $href ) {
  $part=explode("|",$href);
  $parts=count($part);
  if ( $parts === 1 ) { // Name only
   return $part[0];
  } else if ( $parts === 2 ) { // Name|Link
   return '<a href="'.$part[1].'"<spab class="ui-menu-widen-link">'.$part[0].'</span></a>';
  } else if ( $parts === 3 ) { // Icon|Name|Link
   if ( $part[2] == '#' ) { // Stub link
   return '<span class="fa fa-'.$part[0].' menu-fa"></span>'.$part[1];
   } else
   return '<a href="'.$part[2].'"><span class="ui-menu-widen-link"><span class="fa fa-'.$part[0].' menu-fa"></span>'.$part[1].'</span></a>';
  } else if ( $parts === 4 ) {
   if ( $part[2] == '#' ) { // Stub link
   return '<span class="fa fa-'.$part[0].' menu-fa"></span>'.$part[1];
   } else {
    return '<a href="'.$part[2].'" title="'.$part[3].'" alt="'.(isset($part[4])?$part[4]:'').'"><span class="ui-menu-widen-link"><span class="fa fa-'.$part[0].' menu-fa"></span>'.$part[1].'</span></a>';
   }
  }
 }

 function Dropdown_Stacked( $a, $indention='    ' ) {
  $html='';
  foreach ( $a as $n=>$i ) {
  if ( is_array($i) ) {
    $html.=$indention.' '.'<li>'.Dropdown_link($n,'angle-right').'
';
    $html.=$indention.'  '.'<ul>
';
    $html.=Dropdown_Stacked( $i, $indention.'   ' );
    $html.=$indention.'  '.'</ul>
';
    $html.=$indention.' '.'</li>
';
   } else {
    $html.=$indention.'<li>'.Dropdown_link($i).'</li>
';
   }
  }
  return $html;
 }

 function Dropdown_Recurse( $a, $wrapclass="header-menu" ) {
  global $_dd_menu;
  $html='
<div class="'.$wrapclass.'" id="menu_wrap_'.$_dd_menu.'">
<ul id="menu_'.$_dd_menu.'">
';
  foreach ( $a as $named=>$item ) {
   if ( is_array($item) ) {
    $html.=' <li>'.Dropdown_link($named,'angle-down').'
';
    $html.='  '.'<ul>
';
    $html.=Dropdown_Stacked($item);
    $html.='  '.'</ul>
';
    $html.=' '.'</li>
';
   } else {
    $html.=' <li>'.Dropdown_link($item).'</li>
';
   }
  }
  $html.='</ul>
</div>
';
  return $html;
 }

 global $_dd_menu;  $_dd_menu=0;
 global $_Dropdown; $_Dropdown=FALSE;
 function Dropdown( &$p, $a, $return_html=FALSE ) {
  global $_dd_menu,$_Dropdown;
  $_dd_menu++;
  if ( $_Dropdown === FALSE ) {
   $_Dropdown=TRUE;
   $p->CSS('menu.css');
   $p->CSS('font-awesome.min.css');
  }
  $p->JQ('
   $(function() {
    $( "#menu_'.($_dd_menu).'" ).menu({
     icons: { submenu: "ui-icon-triangle-1-e" }
    });
   });
  ');
  if ( $return_html === TRUE ) return Dropdown_Recurse($a);
  else $p->HTML(Dropdown_Recurse($a));
 }

 // Adding the &nbsp; could be automated but this probably more efficient.
 // It's hard to develop a way to calculate how many to add based on your
 // links, but it requires complex interactions with CSS we don't want to
 // get into.  Could be done here, though.

 function Dropdown_Menu( &$p ) {
  $menu= array(
    "edit|Options|#"=>array(
     "# fi-user|Account|account|View your account data",
     "<HR>|#",
     "# fi-graph-bar|Reports|reports|Look at the master list of computed reports",
     "table|View Data|ranged.csv|Download tabular reports of sales data tailored to different formats"
    ),
   );
/*
  if ( Auth::ACL('metrical') && Auth::ACL('su') ) {
   $menu["key|Admin|#|"]=array(
    "ellipsis-h|Datapoints|datapoints|Add datapoint definitions to the Datapoint Description Table",
    "chain|Sets|sets|Define datapoint sets for compound value generation in reports",
    "# fi-graph-trend|Templates|templates|Create and manage templates for reports",
    "# fi-layout|Calculations|calculations|View calculations on a granular level",
   );
  }
*/
  return Dropdown( $p, $menu, TRUE );
 }

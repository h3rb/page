<?php

class TableHelper extends Unique {

 public $settings,$id;

 public function TableHelper( $s=NULL ) {
  $this->Uniqueness();
  if ( is_null($s) ) $s=array();
  $this->Set($s);
 }

 public function Set( $s ) {
  if ( !isset($s['id']) )      $s['id']='a_table';
  if ( !isset($s['columns']) ) $s['columns']=false;
  if ( !isset($s['column']) )  $s['column']=array();
  if ( !isset($s['even']) )    $s['even']='even';
  if ( !isset($s['odd']) )     $s['odd']='odd';
  if ( !isset($s['header']) )  $s['header']='header';
  if ( !isset($s['table']) )   $s['table']='table';
  if ( !isset($s['thead']) )   $s['thead']='td';
  if ( !isset($s['th']) )      $s['th']='th';
  if ( !isset($s['tr']) )      $s['tr']='tr';
  if ( !isset($s['td']) )      $s['td']='td';
  if ( !isset($s['spacer']) )  $s['spacer']='spacer';
  if ( !isset($s['spacing']) ) $s['spacing']='&nbsp;';
  if ( !isset($s['footer']) )  $s['footer']=false;
  if ( !isset($s['widths']) )  $s['widths']=array();
  if ( !isset($s['aligns']) )  $s['aligns']=array();
  $this->settings=$s;
 }

 public function Render( &$html ) {
  $html='<table id="'.$this->settings['id'].'" class="'.$this->settings['table'].'">
';
  if ( isset($this->settings['headings']) ) {
   $html.='<thead class="'.($this->settings['thead']).'">';
   foreach ( $this->settings['headings'] as $heading ) {
    $html.='<th class="'.($this->settings['th']).'">'.$heading.'</th>';
   }
   $html.='</thead>
';
  }
  $html.='<tbody class="'.(isset($this->settings['tbody'])?$this->settings['tbody']:'').'">';
  $i=0;
  foreach ( $this->settings['data'] as $row ) {
   $columns=( $this->settings['columns'] !== false
              ? count($this->settings['columns'])
              : count($row) );
   if ( isset( $row['spacer'] ) ) {
    $html.='<tr class="'.$this->settings['tr'].'" valign="top">';
    for ( $j=0; $j<$columns; $j++ ) $html.='<td class="'.$this->settings['td'].'">'.$this->settings['spacing'].'</td>';
    $html.='</tr>
';
    continue;
   }
   $j=0;
   $html.='<tr class="'.'tr '.($i%2 == 1 ? $this->settings['odd'] : $this->settings['even']).'">';
   if ( $this->settings['columns'] === false ) {
    foreach ( $row as $k=>$v ) {
     if ( isset( $this->settings['column'][$j] ) ) $html.='<td class="'.$this->settings['column'][$j].' '.($this->settings['td']) .'" id="'.$k.'_'.$j.'">';
     else $html.='<td id="'.$k.'_'.$j.'" class="'.$this->settings['td'].'">';
     $html.=$v.'</td>';
    }
   } else {
    foreach ( $this->settings['columns'] as $column ) {
     $k=$column;
     $v=$row[$column];
     if ( isset( $this->settings['column'][$j]) )
      $html.='<td class="'.$this->settings['column'][$j] .'" id="'.$k.'_'.$j.'"'
           .(isset($this->settings['widths'][$j])?' width="'.$this->settings['widths'][$j].'"':'')
           .(isset($this->settings['aligns'][$j])?' align="'.$this->settings['aligns'][$j].'"':'')
           .' valign="top">';
     else $html.='<td class="'.$this->settings['td'].'" id="'.$k.'_'.$j.'" valign="top">';
     $html.=$v.'</td>';
     $j++;
    }
   }
   $html.='</tr>
';
   $i++;
  }
  $html.='</tbody>';
  $html.='</table>
';
 }

 // Helper function for generating a single row, used for creating JQuery AddItem clause
 public function Row( $row, $i=1 ) {
  $columns=( $this->settings['columns'] !== false
             ? count($this->settings['columns'])
             : count($row) );
  $j=0;
  $html='<tr class="'.'tr '.($i%2 == 1 ? $this->settings['odd'] : $this->settings['even']).'">';
  if ( $this->settings['columns'] === false ) {
   foreach ( $row as $k=>$v ) {
    if ( isset( $this->settings['column'][$j] ) )
    $html.='<td class="'.$this->settings['column'][$j].' '.($this->settings['td']) .'" id="'.$k.'_'.$j.'">';
    else $html.='<td id="'.$k.'_'.$j.'" class="'.$this->settings['td'].'">';
    $html.=$v.'</td>';
   }
  } else {
   foreach ( $this->settings['columns'] as $column ) {
    $k=$column;
    $v=$row[$column];
    if ( isset( $this->settings['column'][$j]) )
     $html.='<td class="'.$this->settings['column'][$j] .'" id="'.$k.'_'.$j.'"'
          .(isset($this->settings['widths'][$j])?' width="'.$this->settings['widths'][$j].'"':'')
          .(isset($this->settings['aligns'][$j])?' align="'.$this->settings['aligns'][$j].'"':'')
          .' valign="top">';
    else $html.='<td class="'.$this->settings['td'].'" id="'.$k.'_'.$j.'" valign="top">';
    $html.=$v.'</td>';
    $j++;
   }
  }
  $html.='</tr>';
  return $i;
 }

}

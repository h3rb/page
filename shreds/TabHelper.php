<?php

class TabHelper extends Unique {

 public $tabs, $id, $settings;

 function TabHelper( $a, $s=NULL ) {
  $this->Uniqueness();
  $this->settings-array();
  $this->tabs=$a;
  if ( is_null($s) ) $s=array();
  $this->Set($s);
 }

 function Set($s) {
  if ( !isset($s['clickable']) ) $s['clickable']='1'; // js callback test for if a tab can be clicked
  if ( !isset($s['clicked']) ) $s['clicked']=''; // called when clicked
  if ( !isset($s['css']) ) $s['css']='tab_content';
  if ( !isset($s['tab']) ) $s['tab']='tab';
  if ( !isset($s['active']) ) $s['active']='active';
  if ( !isset($s['activated']) ) $s['activated']=1;
  if ( !isset($s['data']) ) $s['data']='';
  if ( !isset($s['initial']) ) $s['initial']=false;
  if ( !isset($s['class']) ) $s['class']='tabs';
//  if ( isset($s['id']) ) $this->id=$s['id'];
  $this->settings=$s;
 }

 function Render( &$html ) {
  $u=$this->id;
  $html='<table width="100%" cellspacing="0" class="'.$this->settings['class'].'"><tr>';
  if ( $this->settings['initial'] === false ) $js=''; else
  $js='$(document).ready(function(){
    $.ajax({ url:"'.$this->settings['initial'].'", dataType:"html", method:"POST", data: "'.$this->settings['data'].'",
     success: function(data) { $("#tab_content_'.$u.'").html(data) } });
   });
';
  $columns=count($this->tabs);
  $t=1;
  $css=$this->settings['tab'];
  foreach ( $this->tabs as $td ) {
   $tab=$td['tab'];
   $url=$td['url'];
   $data=( isset($td['data']) ? $td['data'] : '' );
   $html.='<td class="'.($t==$this->settings['activated']?$this->settings['active']:$css).'" id="tab_'.$u.'_'.$t.'" onclick="javascript:tab_'.$u.'_'.$t.'_click();">'.$tab.'</td>';
   $js.='function tab_'.$u.'_'.$t.'_click() {
   if ( !('.$this->settings['clickable'].') ) return;
   '.(strlen($this->settings['clicked'])>0?$this->settings['clicked'].'(this);':'').'
   $.ajax({
    url:"'.$url.'",
    dataType: "html",
    method: "POST",
    data: "'.$data.'",
    success: function(data) { $("#tab_content_'.$u.'").html(data); }
   });
  for ( var i=1; i<='.($columns).'; i++ ) {
   $("#tab_'.$u.'_"+i).removeClass( "'.$this->settings['active'].'" );
   $("#tab_'.$u.'_"+i).addClass( "'.$css.'" );
  }
  $("#tab_'.$u.'_'.$t.'").removeClass( "'.$css.'" );
  $("#tab_'.$u.'_'.$t.'").addClass( "'.$this->settings['active'].'" );
}
';
   $t++;
  }
  $html.='</tr><tr><td class="'.$this->settings['css'].'" colspan="'.$columns.'"><div id="tab_content_'.$u.'"></div></td></tr></table>';
  return $js;
 }

};


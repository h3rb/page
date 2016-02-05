<?php

  // Read a paragraph of text and retain the line endings.
  function JSONBindText(
    &$p, $id, $table, $field, $element, $value,
    $placeholder="Enter text", $return_html=FALSE,
    $classes="textentry wide", $rows=-1, $cols=-1 ) {
   global $_bound;
   $fun_name='JSONText_'.$_bound;
   $html=(
    '<textarea '.(isset($p->loaded_bound_plugins) && $p->loaded_bound_plugins === TRUE ? 'data-widearea="enable" ' : '')
      .'placeholder="'.$placeholder.'" name="'.$fun_name.'" id="'.$fun_name.'" class="'.$classes.'"'.
    ( !is_integer($rows) || $rows > 0 ? ' rows="'.$rows.'"' : '' ).
    ( !is_integer($cols) || $cols > 0 ? ' cols="'.$cols.'"' : '' ).
    '>'.$value.'</textarea>'
   );
   $p->JQ('$("#'.$fun_name.'").on("change keypress paste input", function() {
    var evalue=$("#'.$fun_name.'").get(0).value;
    $.ajaxSetup({ cache: false });
    $.ajax({
     cache:false,
     type: "POST",
     dataType: "JSON",
     url:"ajax.bound.json",
     data: { V:evalue, T:"'.$table.'", F:"'.$field.'", I:'.$id.', E:"'.$element.'" }
    }).done(function (e) {
    });
   });');
   $_bound++;
   if ( $return_html === FALSE ) {
    $p->HTML($html);
    return $fun_name;
   }
   return $html;
  }

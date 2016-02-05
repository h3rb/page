<?php

  function AJAXSelect( &$p, $id, $table, $field, $value, $optionarr, $redirect=FALSE ) {
   $options='';
   foreach ( $optionarr as $labeled=>$opt ) {
    $options.='<option value="'.$opt.'"'.( intval($value) == intval($opt) ? ' selected' : '' ).'>'
     .$labeled.'</option>';
   }
   global $_jsel;
   $_jsel++;
   $dom='jsel_'.$_jsel;
   $p->HTML('<select id="'.$dom.'">'.$options.'</select>');
   $p->JQ('
    $("#'.$dom.'").on("click change", function(e){
     var evalue=$("#'.$dom.'").val();
     $.ajaxSetup({ cache: false });
     $.ajax({
      cache:false,
      type: "POST",
      dataType: "JSON",
      url:"ajax.bound",
      data: { V:evalue, T:"'.$table.'", F:"'.$field.'", I:'.$id.' }
     }).done(function(e){
'.($redirect===FALSE?'':'setTimeout(function(){window.location="'.$redirect.'";},3000);').'
     });
    });
   ');
  }


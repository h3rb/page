<?php

 function EditSelect( &$p, $table="Item", $edit_prefix="item.edit", $ajaxjson="items", $jsonvalue=0, $jsonlabel=2, $more='' ) {
  $p->Bind_LoadPlugins();
  global $_es_id;
  $_es_id++;
  $named='es_'.$_es_id;
  $p->HTML(
    '<div>'
     .'<select id="'.$named.'" class="wide80"></select>'
     .'<span id="'.$named.'_edit" class="buttonlink"><span class="fi-pencil"></span> Edit</span>'
     .'</div>');
  $p->JQ('
   $.ajax({
    dataType: "json",
    url: "ajax.json",
    data: {R:"'.$ajaxjson.'"},
    success: function(d){
     $.each(d, function(k,v) {
      var p=new Array(); var i=0;
      $.each(v,function(k,v){p[i]=v; i++});
//      console.log(p);
      $("#'.$named.'").append("<option value=\'"+p['.$jsonvalue.']+"\'>"+p['.$jsonlabel.']+"</option>");
     });

     $("#'.$named.'_edit").on("mouseup",
      function(e){
       var v=$("#'.$named.'").chosen().val();
       if ( parseInt(v) != 0 ) window.location="'.$edit_prefix.'?ID="+v+"'.$more.'";
      });
      '. ( intval($value) > 0 ? ('$("#'.$named.'").val('.$value.');') : '' ).'
      $("#'.$named.'").chosen({
       search_contains:true
      });
     }
    });
  ');
  return $named;
 }

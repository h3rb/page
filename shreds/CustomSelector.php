<?php // A custom selector set that uses ajax.json to populate itself

 global $_cs_id;
 $_cs_id=0;

 function CustomSelectorImages( &$p, $id, $table, $field, $value ) {
  global $_cs_id;
  $_cs_id++;
  $named='cs_images_'.$_cs_id;
  $rnamed='cs_images_resize_'.$_cs_id;
  $p->HTML('<div id="'.$rnamed.'" class="scrollable resize-south"><select id="'.$named.'" ></select></div>');
  $p->JQ('
   $.ajax({
    dataType: "json",
    url: "ajax.json",
    data: {R:"images"},
    success: function(d){
     $.each(d, function(k,v) {
      var p=new Array(); var i=0;
      $.each(v,function(k,v){p[i]=v; i++});
//      console.log(p);
      $("#'.$named.'").append("<option data-img-src=\'"+p[2]+"\' value=\'"+p[0]+"\'>"+p[1]+"</option>");
     });
     $("#'.$named.'").on("select change mousedown mouseup",
      function(e){
       $("#'.$named.'").data("picker").sync_picker_with_select();
       var v=$("#'.$named.'").val();
       $.ajax({
        dataType: "html",
        url: "ajax.bound",
        data: {T:"'.$table.'",F:"'.$field.'",V:v,I:'.$id.'},
        success: function(d) {}
       });
      });
      '. ( intval($value) > 0 ? ('$("#'.$named.'").val('.$value.');') : '' ).'
      $("#'.$named.'").imagepicker({
       show_label: true
      });
 $("#'.$rnamed.'")
      .wrap("<div/>")
        .css({"overflow":"hidden"})
          .parent()
            .css({"display":"inline-block",
                  "overflow":"hidden",
                  "border":"0",
                  "border-bottom":"4px dashed black",
                  "height":function(){return $("#'.$rnamed.'",this).height();},
                  "width":  function(){return $("#'.$rnamed.'",this).width();},
                  "paddingBottom":"12px",
                  "paddingRight":"12px"
                 }).resizable({grid:120,handles:"s"})
                    .find("#'.$rnamed.'")
                      .css({overflow:"auto",
                            width:"100%",
                            height:"100%"});
//      $("#'.$rnamed.'").resizable({grid:120,handles:"s"});
     }
    });
  ');
  return $named;
 }

 function CustomSelectorImages2( &$p, $id, $table, $field, $element, $value ) {
  global $_cs_id;
  $_cs_id++;
  $named='cs_images_'.$_cs_id;
  $rnamed='cs_images_resize_'.$_cs_id;
  $p->HTML(
    '<div id="'.$rnamed.'" class="wide">'
     .'<div><select id="'.$named.'" ></select></div>'
     .'<div id="'.$rnamed.'_result"></div>'
    .'</div>');
  $p->JQ('
       $.ajax({
        dataType: "html",
        url: "ajax.html.stat",
        data: {T:"FileImage",I:"'.$value.'"},
        success: function(d) {
         $("#'.$rnamed.'_result").html(d); }
       });
   $.ajax({
    dataType: "json",
    url: "ajax.json",
    data: {R:"images"},
    success: function(d){
     $.each(d, function(k,v) {
      var p=new Array(); var i=0;
      $.each(v,function(k,v){p[i]=v; i++});
//      console.log(p);
      $("#'.$named.'").append("<option data-img-src=\'"+p[2]+"\' value=\'"+p[0]+"\'>"+p[1]+"</option>");
     });
     $("#'.$named.'").on("select change mousedown mouseup",
      function(e){
       var v=$("#'.$named.'").val();
       $.ajax({
        dataType: "html",
        url: "ajax.bound.json",
        data: {T:"'.$table.'",F:"'.$field.'",E:"'.$element.'",V:v,I:'.$id.'},
        success: function(d) {
        }
       });
       $.ajax({
        dataType: "html",
        url: "ajax.html.stat",
        data: {T:"FileImage",I:v},
        success: function(d) {
         $("#'.$rnamed.'_result").html(d); }
       });
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

 function CustomSelectorImages3( &$p, $id, $table, $field, $value ) {
  global $_cs_id;
  $_cs_id++;
  $named='cs_images_'.$_cs_id;
  $rnamed='cs_images_resize_'.$_cs_id;
  $p->HTML(
    '<div id="'.$rnamed.'" class="wide">'
     .'<div><select id="'.$named.'" ></select></div>'
     .'<div id="'.$rnamed.'_result"></div>'
    .'</div>');
  $p->JQ('
       $.ajax({
        dataType: "html",
        url: "ajax.html.stat",
        data: {T:"FileImage",I:"'.$value.'"},
        success: function(d) {
         $("#'.$rnamed.'_result").html(d); }
       });
   $.ajax({
    dataType: "json",
    url: "ajax.json",
    data: {R:"images"},
    success: function(d){
     $.each(d, function(k,v) {
      var p=new Array(); var i=0;
      $.each(v,function(k,v){p[i]=v; i++});
//      console.log(p);
      $("#'.$named.'").append("<option data-img-src=\'"+p[2]+"\' value=\'"+p[0]+"\'>"+p[1]+"</option>");
     });
     $("#'.$named.'").on("select change mousedown mouseup",
      function(e){
       var v=$("#'.$named.'").val();
       $.ajax({
        dataType: "html",
        url: "ajax.bound",
        data: {T:"'.$table.'",F:"'.$field.'",V:v,I:'.$id.'},
        success: function(d) {
        }
       });
       $.ajax({
        dataType: "html",
        url: "ajax.html.stat",
        data: {T:"FileImage",I:v},
        success: function(d) {
         $("#'.$rnamed.'_result").html(d); }
       });
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

 function CustomSelectorWAVs( &$p, $id, $table, $field, $value ) {
  global $_cs_id;
  $_cs_id++;
  $named='cs_inis_'.$_cs_id;
  $p->HTML('<div><select id="'.$named.'" class="wide"></select></div>');
  $p->JQ('
   $.ajax({
    dataType: "json",
    url: "ajax.json",
    data: {R:"wavs"},
    success: function(d){
     $.each(d, function(k,v) {
      var p=new Array(); var i=0;
      $.each(v,function(k,v){p[i]=v; i++});
//      console.log(p);
      $("#'.$named.'").append("<option value=\'"+p[0]+"\'>"+p[1]+"</option>");
     });
     $("#'.$named.'").on("select change mousedown mouseup",
      function(e){
       var v=$("#'.$named.'").chosen().val();
       $.ajax({
        dataType: "html",
        url: "ajax.bound",
        data: {T:"'.$table.'",F:"'.$field.'",V:v,I:'.$id.'},
        success: function(d) {}
       });
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

 function CustomSelectorFLACs( &$p, $id, $table, $field, $value ) {
  global $_cs_id;
  $_cs_id++;
  $named='cs_inis_'.$_cs_id;
  $p->HTML('<div><select id="'.$named.'" class="wide"></select></div>');
  $p->JQ('
   $.ajax({
    dataType: "json",
    url: "ajax.json",
    data: {R:"flacs"},
    success: function(d){
     $.each(d, function(k,v) {
      var p=new Array(); var i=0;
      $.each(v,function(k,v){p[i]=v; i++});
//      console.log(p);
      $("#'.$named.'").append("<option value=\'"+p[0]+"\'>"+p[1]+"</option>");
     });
     $("#'.$named.'").on("select change mousedown mouseup",
      function(e){
       var v=$("#'.$named.'").chosen().val();
       $.ajax({
        dataType: "html",
        url: "ajax.bound",
        data: {T:"'.$table.'",F:"'.$field.'",V:v,I:'.$id.'},
        success: function(d) {}
       });
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

// generic version
 function CustomSelectorChosen( &$p, $id, $table, $field, $value, $json, $notlist="" ) {
  global $_cs_id;
  $_cs_id++;
  $named='cs_inis_'.$_cs_id;
  $p->HTML('<div><select id="'.$named.'" class="wide"></select></div>');
  $p->JQ('
   $.ajax({
    dataType: "json",
    url: "ajax.json",
    data: {R:"'.$json.'",N:"'.$notlist.'"},
    success: function(d){
     $.each(d, function(k,v) {
      var p=new Array(); var i=0;
      $.each(v,function(k,v){p[i]=v; i++});
//      console.log(p);
      $("#'.$named.'").append("<option value=\'"+p[0]+"\'>"+p[1]+"</option>");
     });
     $("#'.$named.'").on("select change mousedown mouseup",
      function(e){
       var v=$("#'.$named.'").chosen().val();
       $.ajax({
        dataType: "html",
        url: "ajax.bound",
        data: {T:"'.$table.'",F:"'.$field.'",V:v,I:'.$id.'},
        success: function(d) {}
       });
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


 function CustomSortableIDs( &$p, $id, $table, $field, $list, $r_table, $r_field, $json_request, $add="+" ) {
  global $_cs_id;
  $_cs_id++;
  $named='csorts_ids_'.$_cs_id;
  $p->HTML('<ul id="'.$named.'" class="sortable"></ul>');
  $p->HTML('<div><span><select id="'.$named.'_selector" class=""></select></span>');
  $p->HTML('<span id="'.$named.'_add" class="buttonlink">'.$add.'</span></div>');
  $p->JQ('
   $("#'.$named.'").sortable({ attribute:"sort-id" });
   $.ajax({
    dataType: "json",
    method: "POST",
    url: "ajax.json.list",
    data: {L:"'.$list.'", T:"'.$r_table.'", F:"'.$r_field.'"},
    success: function(d){
     $.each(d, function(k,v) {
      var p=new Array(); var i=0;
      $.each(v,function(k,v){p[i]=v; i++;});
      $("#'.$named.'").append("<li sort-id=\'"+p[0]+"\'>"+p[2]+" (#"+p[0]+")</li>");
     });
     $("#'.$named.'").on("stop sortstop sortupdate update",
      function(e,ui){
       '.$named.'_save();
      });
     }
    });
   $.ajax({
    dataType: "json",
    url: "ajax.json",
    data: {R:"'.$json_request.'"},
    success: function(d){
     $.each(d, function(k,v) {
      var p=new Array(); var i=0;
      $.each(v,function(k,v){p[i]=v; i++;});
      $("#'.$named.'_selector").append("<option value=\'"+p[0]+"\'>"+p[1]+" (#"+p[0]+")</option>");
     });
     $("#'.$named.'_selector").chosen({ search_contains:true });
    }
   });
   $("#'.$named.'_add").on("mouseup", function(e){
    var v=$("#'.$named.'_selector").chosen().val();
    var label=$("#'.$named.'_selector option[value=\'"+v+"\']").text();
    $("#'.$named.'").append("<li sort-id=\'"+v+"\'>"+label+"</li>");
    '.$named.'_save();
   });
  ');
  $p->JS('
   function '.$named.'_save() {
       var v = $("#'.$named.'").sortable("toArray",{attribute:"sort-id"}).toString().split(",").join(" ");
       $.ajax({
        method: "POST",
        dataType: "html",
        url: "ajax.bound.list",
        data: {T:"'.$table.'",F:"'.$field.'",L:v,I:'.$id.'},
        success: function(d) {}
       });
   }
  ');
  return $named;
 }

 function CustomSortableImageIDs( &$p, $id, $table, $field, $list, $r_table, $r_field, $json_request="images", $add="+" ) {
  global $_cs_id;
  $_cs_id++;
  $named='csorts_ids_'.$_cs_id;
  $p->HTML('<div class="formhighright">');
  $p->HTML(' <span><select id="'.$named.'_selector" class=""></select></span>');
  $p->HTML(' <div>Append this image: <span id="'.$named.'_add" class="buttonlink">+</span></div>');
  $p->HTML('</div>');
  $p->HTML('<div class="fifty max256 smallfonts">');
  $p->HTML('<ul id="'.$named.'" class="sortable"></ul>');
  $p->HTML('</div>');
  $p->JQ('
   $("#'.$named.'").sortable({ attribute:"sort-id" });
   $.ajax({
    dataType: "json",
    method: "POST",
    url: "ajax.json.list",
    data: {L:"'.$list.'", T:"'.$r_table.'", F:"'.$r_field.'"},
    success: function(d){
     $.each(d, function(k,v) {
      var p=new Array(); var i=0;
      $.each(v,function(k,v){p[i]=v; i++;});
      $("#'.$named.'").append("<li sort-id=\'"+p[0]+"\'>"+p[3]+"<DIV ID=\''.$named.'Remo"+p[0]+"\' title=\'Remove\' class=\'bare upright\'><SPAN CLASS=\'fi-x buttonsmred\'></SPAN></DIV></li>");
      $("#'.$named.'Remo"+p[0]).click(function(e){
       $(this).parent().remove();
       '.$named.'_save();
      });
     });
     $("#'.$named.'").on("stop sortstop sortupdate update",
      function(e,ui){
       '.$named.'_save();
      });
     }
    });
   $.ajax({
    dataType: "json",
    url: "ajax.json",
    data: {R:"'.$json_request.'",N:0},
    success: function(d){
     $.each(d, function(k,v) {
      var p=new Array(); var i=0;
      $.each(v,function(k,v){p[i]=v; i++;});
      $("#'.$named.'_selector").append("<option value=\'"+p[0]+"\'>"+p[1]+" (#"+p[0]+")</option>");
     });
     $("#'.$named.'_selector").chosen({ search_contains:true });
    }
   });
   $("#'.$named.'_add").on("mouseup", function(e){
    var v=$("#'.$named.'_selector").chosen().val();
    $.ajax({
     dataType: "html",
     method: "POST",
     url: "ajax.html.stat",
     data: {T:"'.$r_table.'",I:v},
     success: function(d) {
      var label=d;//$("#'.$named.'_selector option[value=\'"+v+"\']").html();
      $("#'.$named.'").append("<li sort-id=\'"+v+"\'>"+label+"<DIV ID=\''.$named.'Rem"+v+"\' title=\'Remove\' class=\'bare upright\'><SPAN CLASS=\'fi-x buttonsmred\'></SPAN></DIV></li>");
      $("#'.$named.'Rem"+v).click(function(e){
       $(this).parent().remove();
       '.$named.'_save();
      });
      '.$named.'_save();
     }
    });
   });
  ');
  $p->JS('
   function '.$named.'_save() {
       var v = $("#'.$named.'").sortable("toArray",{attribute:"sort-id"}).toString().split(",").join(" ");
       $.ajax({
        method: "POST",
        dataType: "html",
        url: "ajax.bound.list",
        data: {T:"'.$table.'",F:"'.$field.'",L:v,I:'.$id.'},
        success: function(d) {}
       });
   }
  ');
  return $named;
 }

// generic one
 function CustomSelector( &$p, $id, $table, $field, $value, $ajaxjson, $jsonvalue=0, $jsonlabel=2 ) {
  global $_cs_id;
  $_cs_id++;
  $named='cs_inis_'.$_cs_id;
  $p->HTML('<div><select id="'.$named.'" class="wide"></select></div>');
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
     $("#'.$named.'").on("select change mousedown mouseup",
      function(e){
       var v=$("#'.$named.'").chosen().val();
       $.ajax({
        dataType: "html",
        url: "ajax.bound",
        data: {T:"'.$table.'",F:"'.$field.'",V:v,I:'.$id.'},
        success: function(d) {}
       });
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

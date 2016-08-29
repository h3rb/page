<?php



 global $ajax_selector_id; $ajax_selector_id=0;
 function AJAXSelector( &$p, $table=1, $length=20, $callback="myGlobalCallback", $scrolly=TRUE ) {
  global $ajax_selector_id; $ajax_selector_id++;
  $id=$ajax_selector_id;
  $search_id='asel-'.$id.'-search';
  $search_button_id='asel-'.$id.'-search-button';
  $content_id='asel-'.$id.'-content';
  $prev_id='asel-'.$id.'-prev';
  $prev_disabled_id='asel-'.$id.'-prev-disabled';
  $next_id='asel-'.$id.'-next';
  $next_disabled_id='asel-'.$id.'-next-disabled';
  $pages_id='asel-'.$id.'-pages';
  $p->HTML('
   <div class="wide">
   <input class="wide70" type="text" id="'.$search_id.'"> <button id="'.$search_button_id.'"><span class="fi-magnifying-glass"></span></button>
   <button id="'.$prev_id.'" class="buttonlink">&ltcc;</button>
   <button id="'.$prev_disabled_id.'" class="buttonlinkdisabled" title="You are on the first page.">&ltcc;</button>
   <button id="'.$next_id.'" class="buttonlink">&gtcc;</button>
   <button id="'.$next_disabled_id.'" class="buttonlinkdisabled" title="You are on the last page.">&gtcc;</button>
   <span class="circle"><span id="'.$pages_id.'" class="padded"></span></span>
   </div>
   <div class="'.($scrolly===TRUE?'wide scrollable resize-south':'').'" id="'.$content_id.'"><center><img src="i/LOAD.GIF"></center></div>
');
  $p->JQ('
//   $("#'.$search_id.'").on("change",function(e){
//   });
   $("#'.$search_id.'").on("keydown",function(e){
     if ( e.keyCode == 13 ) $("#'.$search_button_id.'").click();
   });
   $("#'.$search_button_id.'").on("click",function(e){
    asel_'.$id.'_page=0;
    asel_'.$id.'_get();
    $("#'.$prev_id.'").hide();
    $("#'.$prev_disabled_id.'").show();
    $("#'.$pages_id.'").text("");
    $("#'.$pages_id.'").hide();
   });
   $("#'.$prev_id.'").on("click",function(e){
    var p=asel_'.$id.'_page;
    p--; if ( p <= 0 ) {
     $("#'.$prev_id.'").hide();
     $("#'.$prev_disabled_id.'").show();
     p=0;
     $("#'.$pages_id.'").text("");
     $("#'.$pages_id.'").hide();
    } else
    $("#'.$pages_id.'").text(""+asel_'.$id.'_page);
    asel_'.$id.'_page=p;
    asel_'.$id.'_get();
   });
   $("#'.$next_id.'").on("click",function(e){
    $("#'.$prev_id.'").show();
   $("#'.$prev_disabled_id.'").hide();
    var p=asel_'.$id.'_page;
    p++;
    asel_'.$id.'_page=p;
    asel_'.$id.'_get();
    $("#'.$pages_id.'").text(""+asel_'.$id.'_page);
    $("#'.$pages_id.'").show();
   });
   $("#'.$prev_id.'").hide();
   $("#'.$next_id.'").hide();
   $("#'.$pages_id.'").hide();
   asel_'.$id.'_get();
');
  if ( $scrolly === TRUE ) {
   $p->JQ('
    $("#'.$content_id.'")
      .wrap("<div id=\\"wider-wrapper'.$id.'\\"/>")
        .css({"overflow":"hidden"})
          .parent()
            .css({"display":"inline-block",
                  "overflow":"hidden",
                  "border":"0",
                  "border-bottom":"4px dashed black",
                  "height":function(){return $("#'.$content_id.'",this).height();},
                  "width":  function(){return $("#'.$content_id.'",this).width();},
                  "paddingBottom":"12px",
                  "paddingRight":"12px"
                 }).resizable({grid:120,handles:"s"})
                    .find("#'.$content_id.'")
                      .css({overflow:"auto",
                            width: "100%",
                            height:"100%"});
   var parent_width'.$id.'=$("#wider-wrapper'.$id.'").parent().width();
   $("#wider-wrapper'.$id.'").css({width: parent_width'.$id.'});
');
  }
  $p->JS('
   var asel_'.$id.'_page=0;
   function asel_'.$id.'_get() {
    $("#'.$content_id.'").html("<center><img src=\\"i/LOAD.GIF\\"></center>");
    search_value=$("#'.$search_id.'").val();
    $.getJSON( "ajax.selector",
     { T:'.$table.', L:'.$length.', P:asel_'.$id.'_page, S:search_value },
     function(json){
      console.log(json);
      var items = [];
      $.each( json, function( key, v ) {
       items.push("<div class=\\"ajax-selectable connect-left\\" onclick=\\"javascript:'.$callback.'("+v.FileMeta_ID+");\\"><center><div class=\\"thumb100\\"><img src=\\""+v.thumbnail+"\\" border=0></div></center>"+v.Name+"<BR>"+v.Descriptive+"</div>");
      });
      if ( items.length < '.$length.' ) {
       $("#'.$next_id.'").hide();
       $("#'.$next_disabled_id.'").show();
      } else {
       $("#'.$next_id.'").show();
       $("#'.$next_disabled_id.'").hide();
      }
      $("#'.$content_id.'").html(items.join(""));
     }
    );
   }
');
 }
 
 global $ajax_image_selector_id; $ajax_image_selector_id=0;
 function AjaxImageSelector( &$p, $image_name, $table, $table_row_id, $field, $value, $length=20, $none=TRUE ) {
  global $ajax_image_selector_id; $ajax_image_selector_id++;
  $a_id=$ajax_image_selector_id;
  global $database;
  $fileimage_model=new FileImage($database);
  $p->HTML('<div class="formemphasis2 wide">');
  $p->HTML('<table width="100%"><tr class="no-hover"><td width="300" class="no-hover" id="ajax-image-preview'.$a_id.'">');
  $p->HTML('<h3>'.$image_name.'</h3>');
  $icon=NULL;
  if ( intval($value) > 0 ) $icon=$fileimage_model->GetAll($value);
  if ( false_or_null($icon) ) $p->HTML('No image.');
  else {
   $p->HTML('<img src="'.$fileimage_model->ThumbName($icon,256).'"><BR>'.$icon['File']['Name']);
  }
  $p->HTML('</td><td class="no-hover" width="*">');
  AJAXSelector( $p, 1, $length, "ajax_image_preview_".$a_id );
  $p->HTML('</td></tr></table>');
  $p->HTML('</div>');
  $p->JS('
  function ajax_image_preview_'.$a_id.'( fileimage_id ) {
       $.ajax({
        dataType: "html",
        url: "ajax.bound",
        data: {T:"'.$table.'",F:"'.$field.'",V:fileimage_id,I:'.$table_row_id.'},
        success: function(d) {
        }
       });
       $.ajax({
        dataType: "html",
        url: "ajax.html.stat",
        data: {T:"FileImage",I:fileimage_id,X:256},
        success: function(d) {
         $("#ajax-image-preview'.$a_id.'").html("<h3>'.$image_name.'</h3>"+d); }
       });
  }
');
  if ( $none === TRUE ) {
   $p->HTML('<button id="image-remove-'.$a_id.'" class="buttonlink"><span class="fa fa-sign-out"></span> Remove Image</button>');
   $p->JQ(' $("#image-remove-'.$a_id.'").on("click",function(e){ajax_image_preview_'.$a_id.'(0);}); ');
  }
 }

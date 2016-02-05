<?php

 global $_ui_clicktoopen;

 $_ui_clicktoopen=0;

 function click_to_open(&$p,$inner,$outer,$content,$offset=64,$tag='div') {
  global $_ui_clicktoopen; $_ui_clicktoopen++;
  $cid='c2o'.$_ui_clicktoopen;
  $p->JQ('
   $("#O'.$cid.'").hide(); $("#C'.$cid.'").hide();
   $("#I'.$cid.'").click(function(e){ $("#I'.$cid.'").hide(); $("#O'.$cid.'").show(); $("#C'.$cid.'").show(); $("html,body").animate({scrollTop: $("#O'.$cid.'").offset().top-'.$offset.'},1000); });
   $("#O'.$cid.'").click(function(e){ $("#I'.$cid.'").show(); $("#O'.$cid.'").hide(); $("#C'.$cid.'").hide(); });
  ');
  return (
   '<'.$tag.' id="I'.$cid.'">'.$inner.'</'.$tag.'>'.
   '<'.$tag.' id="O'.$cid.'">'.$outer.'</'.$tag.'>'.
   '<'.$tag.' id="C'.$cid.'">'.$content.'</'.$tag.'>'
  );
 }

 function centered($content) { return '<center>'.$content.'</center>'; }

 global $_ui_clicktoload;
 $_ui_clicktoload=0;
 function click_to_load(&$p,$inner,$outer,$url,$param='',$loading='Loading..please wait.') {
  global $_ui_clicktoopen; $_ui_clicktoopen++;
  $param_str='';
  if ( is_array($param) ) {
   foreach ( $param as $key=>$valye ) $param_str.=','.$key.':"'.$value.'"';
   $param_str=ltrim($param_str,',');
  } else if ( strlen($param) == 0 ) {
  } else {
   $param_str=$param;
  }
  $cid='c2o'.$_ui_clicktoopen;
  $p->JS('
   function '.$cid.'() {
    $("#C'.$cid.').get(0).innerHTML="'.str_replace('"','\\"',$loading).'";
    $.ajax({
     type: "POST",
     dataType: "html",
     data: {'.$param_str.'},
     success: function(d) {
      $("#C'.$cid.').get(0).innerHTML=d;
     }
    });
   }
  ');
  $p->JQ('
   $("#O'.$cid.'").hide(); $("#C'.$cid.'").hide();
   $("#I'.$cid.'").click(function(e){ $("#I'.$cid.'").hide(); $("#O'.$cid.'").show(); $("#C'.$cid.'").show(); });
   $("#O'.$cid.'").click(function(e){ $("#I'.$cid.'").show(); $("#O'.$cid.'").hide(); $("#C'.$cid.'").hide(); });
  ');
  return (
   '<div id="I'.$cid.'">'.$inner.'</div>'.
   '<div id="O'.$cid.'">'.$outer.'</div>'.
   '<div id="C'.$cid.'"></div>'
  );
 }

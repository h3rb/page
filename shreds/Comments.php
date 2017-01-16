<?php

 global $_comments;
 $_comments=1;

 function Comments( &$p, $table, $id, $field ) {
  global $database,$auth;
  $m=new $table($database);
  $r=$m->Get($id);
  if ( false_or_null($r) ) {
   $p->HTML('<div>Comments could not be loaded.</div>');
  }
  global $_comments;
  $_comments++;
  $p->JSONJS( "jsoncomments".$_comments, $r[$field] );
  $p->JS('
   function comment_open_'.$_comments.'(me,i,j) {
    $(me).hide();
    $("#comment_f_"+i+"_"+j).show();
   }
   function reply_comment_'.$_comments.'(i,j) {
    var fid="comment_f_"+i+"_"+j;
    var _form=$("#"+fid).get(0);
    var _text=$("#comment_t_"+i+"_"+j).get(0).value;
    if ( _text.length < 1 ) {
     alert("You cannot post a blank comment.");
     return;
    }
    var cdata = {fid:fid,root:i,j:j,comment:_text};
    alert("reply to "+i+","+j+" = "+_text);
    $.ajax({
     url:"ajax.comment",
     data: { ajax:1, T:"'.$table.'", I:"'.$id.'", F:"'.$field.'", _i:i, _j:j, comment:_text },
     context: cdata,
     success: function (e) {
      //alert("Comment posted!");
      console.log(cdata);
      var fid=cdata.fid;
      var index=cdata.root;
      var i=cdata.j;
      var jsid=index+"_"+i;
      $("#"+fid).replaceWith(
       "<div><a href=\\"profile?ID='.$auth['ID'].'\\">'.$auth['username'].'</a><p>"
       +cdata.comment+"<span class=\\"buttonlink handy\\" onclick=\\"javascript:comment_open_'.$_comments.'(this,"+index+","+i+")\\"><span class=\\"fi-arrow-down\\"></span></span></p>"
       +"&#8627;<form style=\\"display:none;\\" id=\\"comment_f_"+jsid+"\\">"
        +"&nbsp;&rarr;<textarea id=\\"comment_t_"+jsid+"\\" placeholder=\\"Enter your comment here...\\"></textarea>"
        +"<span class=\\"buttonlink handy\\" onclick=\\"javascript:reply_comment_'.$_comments.'("+index+","+i+")\\"><span class=\\"fa fa-reply\\"></span></span>"
        +"</form>"
       +"</div>"
      );
     },
     fail: function(e) {
      form.innerHTML+="<p><span class=\\"fa fa-warning\\"></span> Your comment could not be saved.</p>";
     }
    });
   }
   function populate_comments_'.$_comments.'_recurse(index,container,node) {
    var interior=document.createElement("div");
    container.appendChild(interior);
    $.each(node,function(i,item){
     var jsid=index+"_"+i;
     $(interior).append(
      "<div><a href=\\"profile?ID="+item.uid+"\\">"
       +item.username+"</a><p>"
       +item.comment+"<span class=\\"buttonlink handy\\" onclick=\\"javascript:comment_open_'.$_comments.'(this,"+index+","+i+")\\"><span class=\\"fi-arrow-down\\"></span></span></p>"
       +(index>0?"&#8627;":"")+"<form style=\\"display:none;\\" id=\\"comment_f_"+jsid+"\\">"
        +"&nbsp;&rarr;<textarea id=\\"comment_t_"+jsid+"\\" placeholder=\\"Enter your comment here...\\"></textarea>"
        +"<span class=\\"buttonlink handy\\" onclick=\\"javascript:reply_comment_'.$_comments.'("+index+","+i+")\\"><span class=\\"fa fa-reply\\"></span></span>"
        +"</form>"
       +"</div>"
     );
     populate_comments_'.$_comments.'_recurse(index+1,interior,item.replies);
    });
   }
   function populate_comments_'.$_comments.'() {
    var where=$("#comments-container-'.$_comments.'").get(0);
    var data=jsoncomments'.$_comments.';
    populate_comments_'.$_comments.'_recurse(1,where,data);
    $(where).append(
     "<div>"+
     "<form id=\\"comment_f_0_0\\">"
      +"<textarea id=\\"comment_t_0_0\\" placeholder=\\"Enter your comment here...\\"></textarea>"
      +"<span class=\\"buttonlink handy\\" onclick=\\"javascript:reply_comment_'.$_comments.'(0,0)\\"><span class=\\"fa fa-send\\"></span></span>"
     +"</form>"
     +"</div>"
    );
   }
  ');
  $p->JQ('
   populate_comments_'.$_comments.'();
  ');
  $p->HTML('
    <div id="comments-container-'.$_comments.'"></div>
  ');
 }






<?php

 function LockCheck ( $table, $id ) {
  if ( matches($table,"Modified") ) return FALSE;
  global $database;
  $m=new RowLock($database);
  $locks = $m->Select(array('I'=>$table,'T'=>$id));
  if ( !false_or_null($locks) && count($locks) > 0 ) return TRUE;
  return FALSE;
 }

 function LockMechanism( &$p, $table, $id ) {
  $locked=LockCheck($table,$id);
  if ( matches($table,"Modified") ) return;
  $p->HTML('<div class="padlockarea"><span id="padlock" class="redbutton">'
          .'<span id="padlockface" class=""></span>'
          .'</span>'
          .'<span id="padlockmsg"></span>'
          .'</div>'
  );
  $p->JQ('
       $.ajax({
           url:"ajax.skeleton.key",
          data:{S:1,T:"'.$table.'",I:'.$id.'},
      dataType:"html",
       success:function(d){
       page_lock_status=parseInt(d) == 1 ? true : false;
       if ( page_lock_status ) {
        $("#padlockface").removeClass("fi-unlock");
        $("#padlockface").addClass("fi-lock");
        $("#padlockmsg").get(0).innerHTML=" locked";
       } else {
        $("#padlockface").removeClass("fi-lock");
        $("#padlockface").addClass("fi-unlock");
        $("#padlockmsg").get(0).innerHTML=" ";
       }
      }
     });
   var page_lock_status='.($locked?'true':'false').';
   $("#padlock").on("click",function(e){
    $.ajax({
          url:"ajax.skeleton.key",
         data:{T:"'.$table.'",I:'.$id.'},
     dataType:"html",
      success:function(d){
      page_lock_status=parseInt(d) == 1 ? true : false;
      if ( page_lock_status ) {
       $("#padlockface").removeClass("fi-unlock");
       $("#padlockface").addClass("fi-lock");
       $("#padlockmsg").get(0).innerHTML=" locked";
      } else {
       $("#padlockface").removeClass("fi-lock");
       $("#padlockface").addClass("fi-unlock");
       $("#padlockmsg").get(0).innerHTML=" ";
      }
     }
    });
    setInterval(function(){
     $.ajax({
           url:"ajax.skeleton.key",
          data:{S:1,T:"'.$table.'",I:'.$id.'},
      dataType:"html",
       success:function(d){
       page_lock_status=parseInt(d) == 1 ? true : false;
       if ( page_lock_status ) {
        $("#padlockface").removeClass("fi-unlock");
        $("#padlockface").addClass("fi-lock");
        $("#padlockmsg").get(0).innerHTML=" locked";
       } else {
        $("#padlockface").removeClass("fi-lock");
        $("#padlockface").addClass("fi-unlock");
        $("#padlockmsg").get(0).innerHTML=" ";
       }
      }
     });
    }, 15000 );
   });
  ');
 }

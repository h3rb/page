<?php

 function AutoLockCheck( $table, $id ) {
  plog("AutoLockCheck: Start");
  global $database,$auth,$auth_model;
  $m=new AutoRowLock($database);
  // Expire old locks
  $m->Delete('Timestamp < '.strtotime('-10 minutes'));
  $locks=$m->Select(
   array('I'=>$ID,'T'=>$Table)
  );
  if ( !false_or_null($locks) && count($locks) > 0 ) {
   $found=false;
   $others=0;
   foreach ( $locks as $lock ) {
    if ( $m->Expired($lock) ) $m->Delete(array('ID'=>$lock['ID']));
    else if ( $m->LockedByMe($lock) ) {
     if ( $found === true ) $m->Delete(array('ID'=>$lock['ID']));
     else {
      $m->RefreshLock($lock);
      $found=true;
     }
    }
    else $others++;
   }
   return ($found === true || $others===0);
  }
  plog("AutoLockCheck: End");
  return FALSE;
 }

 function AutoLockMechanism( &$p, $table, $id ) {
  plog("AutoLockMechanism: Start");
  $p->HTML('<div class="apadlockarea">'
          .'<span id="apadlockface" class=""></span>'
          .' <span id="apadlockmsg"></span>'
          .'</div>'
  );
  $p->JS('
   var page_was_locked=false;
   function set_row_mutex(d) {
       var msg=$("#apadlockmsg").get(0).innerHTML;
       var first_check=(msg.length == 0 );
       var page_unlocked_to_me= ( d.unlocked == 1 );
       if ( !page_unlocked_to_me ) {
        $("input").attr("readonly", "readonly");
        $("textarea").attr("readonly", "readonly");
        $("select").hide();
        page_was_locked=true;
        $("#apadlockface").addClass("fi-lock");
        $("#apadlockarea").addClass("apadlock");
        $("#apadlockmsg").get(0).innerHTML=d.message;
       } else
       if ( !first_check ) {
        if ( page_was_locked && page_unlocked_to_me ) document.location.reload(true);
       } else {
        $("#apadlockface").removeClass("fi-lock");
        $("#apadlockmsg").get(0).innerHTML="You have edit control";
       }
   }
  ');
  $p->JQ('
       $.ajax({
           url:"ajax.auto.skeleton.key",
          data:{T:"'.$table.'",I:'.$id.'},
      dataType:"json",
       success:function(d){ set_row_mutex(d); }
     });
    setInterval(function(){
     $.ajax({
           url:"ajax.auto.skeleton.key",
          data:{T:"'.$table.'",I:'.$id.'},
      dataType:"json",
       success:function(d){ set_row_mutex(d); }
     });
    }, 5000 );
  ');
  plog("AutoLockMechanism: End");
 }

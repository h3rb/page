<?php // Ajax intake for auto-locking edit pages

 include 'core/Page.php';

  if ( !Session::logged_in() ) die;

  $getpost=getpost();
  if ( !isset($getpost['I']) || !isset($getpost['T']) ) die;

  $ID=$getpost['I'];
  $Table=$getpost['T'];


  if ( !Auth::ACL('edit-'.$Table) && !Auth::ACL('edit-'.$Table.'-'.$Field) && !Auth::ACL('su') ) {
   echo json_encode(array("message"=>'Read Only', "unlocked"=>0) );
   exit;
  }

  global $auth_database,$auth,$auth_model;

  $m=new AutoRowLock($auth_database);

  $m->Delete('Timestamp < '.strtotime('-10 minutes'));  // Expire old locks
  $locks=$m->Select(  array('I'=>$ID,'T'=>$Table) ); // Find existing locks
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
   if ( $found === false && $others > 0 ) { // Locked up
    echo json_encode(array("message"=> ($m->LockedTo($lock).' has edit control'), "unlocked"=>0 ));
    die;
   } else { // Locked to user
    echo json_encode(array("message"=>'You have edit control',"unlocked"=>1));
    die;
   }
  }

  // Locking it up
  $m->LockToMe($Table,$ID);
  echo json_encode(array("message"=>'You have edit control',"unlocked"=>1));


<?php // Ajax intake for bound form elements

// global $plog_level; $plog_level=1;
 include 'core/Page.php';

 if ( Session::logged_in() ) {
  global $database;
  $getpost=getpost();
  if ( isset($getpost['I']) && isset($getpost['T']) ) {
   $ID=$getpost['I'];
   $Table=$getpost['T'];

   if ( !Auth::ACL('edit-'.$Table) && !Auth::ACL('edit-'.$Table.'-'.$Field) && !Auth::ACL('su') ) { echo '1'; die; }
   $m=new RowLock($database);
   $locks=$m->Select(array('I'=>$ID,'T'=>$Table));
   if ( isset($getpost['S']) ) { // checking status only
    if ( !false_or_null($locks) && count($locks) > 0 ) { echo '1'; die; }
    echo '0'; die;
   }
   if ( !false_or_null($locks) && count($locks) > 0 ) {
    foreach ( $locks as $lock ) $m->Delete(array('ID'=>$lock['ID']));
    echo '0'; die;
   }
   $m->Insert(array('T'=>$Table,'I'=>$ID));
   Modified( array( "D"=>array($Table=>array("F"=>'Edit Lock',"I"=>$ID))) );
   echo '1'; die;
  }
 }

 echo '-1'; die; // Give a false positive

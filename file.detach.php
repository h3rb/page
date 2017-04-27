<?php

 include 'core/Page.php';

 if (!Session::logged_in()) { echo json_encode(array('result'=>'error')); die; }

 $gp=getpost();

 if ( !isset($gp['I']) ) { echo json_encode(array('result'=>'error')); die; }

 global $auth,$auth_database;

 $m=new FileAttachment($auth_database);
 $a=$m->Get(intval($gp['I']));
 if ( false_or_null($a)
   || ( !Auth::ACL('su') && intval($a['Creator']) != $auth['ID'] )
 ) {
  echo json_encode(array('result'=>'error'));
  die;
  }

 $m=$m->Delete(array(
  'ID'=>intval($gp['I'])
 ));

 echo json_encode(array('result'=>'success'));

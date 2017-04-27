<?php

 global $plog_level; $plog_level=1;
 include 'core/Page.php';

 if (!Session::logged_in()) echo json_encode(array('result'=>'error'));

 $gp=getpost();

 if ( !isset($gp['T']) && !isset($gp['I']) && !isset($gp['F']) && !isset($gp['FID']) ) echo json_encode(array('result'=>'error'));

 global $auth,$auth_database;

 $m=new FileAttachment($auth_database);

 $new_id=$m->Insert(array(
  "RefTable"=>$gp['T'],
  "Ref"=>intval($gp['I']),
  "FileTable"=>$gp['F'],
  "FileRef"=>intval($gp['FID']),
  "Notes"=>'',
  "Creator"=>$auth['ID'],
  "Created"=>strtotime('now')
 ));

 if ( $new_id > 0 ) echo json_encode(array('result'=>'success'));
 else echo json_encode(array('result'=>'error'));

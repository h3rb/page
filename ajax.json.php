<?php // Ajax intake for JSON data requests, this was used to handle specific custom selectors

 include 'core/Page.php';

 if ( Session::logged_in() ) {
  global $database;
  $getpost=getpost();
  if ( isset($getpost['R']) ) {
   if ( matches($getpost['R'],'wavs') ) {
    $result=array();
    $s_model=new FileWAV($database);
    $ss=$s_model->All('ORDER BY ID DESC');
    $files=new File($database);
    $i=1;
    $result[0]=array( 0=>0, 1=>"None" );
    if ( !false_or_null($ss) ) foreach ( $ss as $s ) {
     $f=$files->Get($s['r_File']);
     $result[$i++]=array(
      0=>intval($s['ID']),
      1=>$f['Name'],
      2=>$f['Name'].' #'.$s['ID']
     );
    }
    echo json_encode($result);
    exit;
   } else if ( matches($getpost['R'],'flacs') ) {
    $result=array();
    $s_model=new FileFLAC($database);
    $ss=$s_model->All('ORDER BY ID DESC');
    $files=new File($database);
    $i=1;
    $result[0]=array( 0=>0, 1=>"None" );
    if ( !false_or_null($ss) ) foreach ( $ss as $s ) {
     $f=$files->Get($s['r_File']);
     $result[$i++]=array(
      0=>intval($s['ID']),
      1=>$f['Name'],
      2=>$f['Name'].' #'.$s['ID']
     );
    }
    echo json_encode($result);
    exit;
   } else if ( matches($getpost['R'],'images') ) {
    $result=array();
    $images_model=new FileImage($database);
    $images=$images_model->All('ORDER BY ID DESC');
    $files=new File($database);
    $i=0;
    if ( !isset($getpost['N']) )
     $result[$i++]=array(
      0=>0, 1=>"None", 2=>"i/none64.png", 3=>'<div><span class="enormous">X</span></div>'
     );
    if ( !false_or_null($images) ) foreach ( $images as $s ) { // Return available image file ids
     $f=$files->Get($s['r_File']);
     if ( !false_or_null($f) ) {
      $tname=$files->ThumbName($f);
      $result[$i++]=array(
       0=>intval($s['ID']),
       1=>$f['Name'].' #'.$f['ID'].' '.$s['Width'].'x'.$s['Height'],
       2=>$files->ThumbName($f),
       3=>$f['ID'],
       4=>'<img src="'.$tname.'"><div>'.$f['Name'].' (#'.$item['ID'].')</div><div class="json-stat-image">'.$stats.'</div>'
      );
     }
    }
    echo json_encode($result);
    exit;
   } else { // Catch-all generic response for table
    if ( file_exists('model/'.$getpost['R'].'.php') ) {
     $classname=$getpost['R'];
     $result=array();
     $model=new $classname($database);
     $rows=$model->All();
     $i=1;
     $result[0]=array(0=>'0',1=>'None',2=>'None');
     if ( !false_or_null($rows) ) foreach ( $rows as $s ) {
      $result[$i++]=array(
       0=>intval($s['ID']),
       1=>$s['Name'],
       2=>$s['Name'].' #'.$s['ID']
      );
     }
     echo json_encode($result);
     exit;
    }
   }
  }
 }

 echo '{"result":"error"}';

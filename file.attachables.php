<?php

 include 'core/Page.php';

 if ( !Session::logged_in() ) die;
 // get attachable files for user

 global $auth, $auth_database;

 $f_model=new File($auth_database);
 $fi_model=new FileImage($auth_database);

 if ( Auth::ACL('su') ) $files=$f_model->All("ORDER BY Uploaded DESC");
 else $files=$f_model->Select(array("Uploader"=>$auth['ID']),"*","","Uploaded DESC");

 if ( false_or_null($files) ) {
  echo 'You have not uploaded any files yet.'; die;
 }

 echo '<table>';
 foreach ( $files as $f ) {
  $fi=$fi_model->byFile($f['ID']);
  echo '<tr>';
  if ( !false_or_null($fi) ) {
   $table='FileImage';
   echo '<td><img src="'.$fi_model->ThumbName($fi).'"></td>';
  } else {
   $table='File';
   echo '<td>'.$f['Name'].'</td>';
  }
  echo '<td>'.human_datetime($f['Uploaded']).'</td>';
  echo '<td><button onclick="javascript:add_attachment(\''.$table.'\','.$f['ID'].');" class="tinybutton"><span class="fi-plus"></span> Attach</button></td>';
  echo '</tr>';
 }
 echo '</table>';

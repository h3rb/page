<?php //ajax

 include 'core/Page.php';

 if (!Session::logged_in()) echo json_encode(array("result"=>"error"));

 $gp=getpost();

 if ( !isset($gp['T']) && !isset($gp['I']) ) echo json_encode(array("result"=>"error"));

 global $auth,$auth_database;

 $m=new FileAttachment($auth_database);
 $f=new File($auth_database);
 $fi=new FileImage($auth_database);

 $attachments=$m->Select(array("RefTable"=>$gp['T'],"Ref"=>intval($gp['I'])));

 echo '<div class="formhighlight">';
 echo '<table width="90%">';
 foreach ( $attachments as $a ) {
  $file=$f->Get($a["FileRef"]);
  $img=NULL;
  echo '<tr>';
  if ( $a["FileTable"] == "File" ) {
   echo '<td>'.$file["Name"].'</td>';
  } else if ( $a["FileTable"] == "FileImage" ) {
   $file=$f->Get($a['FileRef']);
   $img=$fi->byFile($file['ID']);
   $thumb=$fi->ThumbName($img,200);
   echo '<td><img src="'.$thumb.'"><BR>'.$file['Name'].'</td>';
  }
  echo '<td>';
  echo 'Attached: '.human_datetime($a['Created']).'<BR>';
  echo 'Uploaded: '.human_datetime($file['Uploaded']).'<BR>';
  echo '<a class="buttonlink" href="files.download?ID='.$a["FileRef"].'"><span class="fi fi-download"></span> Download</a><BR>';
  echo '<button class="tinybutton" onclick="javascript:remove_attachment('.$a['ID'].');"><span class="fi fi-x"></span> Detach</a><BR>';
  echo '</td>';
  echo '</tr>';
 }
 echo '</table>';
 echo '</div>';

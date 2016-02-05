<?php
 include 'core/Page.php';

 $getpost = getpost();
 $getpost['m']=intval($getpost['m']);

 $p=new Page();

 $p->CSS( 'main.css' );

 $message=NULL;
 switch ( $getpost['m'] ) {
  case 1: case 2: $message='Password or Username Incorrect'; break;
  case 4: $message='Account is locked.'; break;
 }
 if ( !is_null($message)) {
  $messages=array( '###Messages###'=>$message );
 } else $messages=array( '###Messages###'=>'' );

 $p->HTML('login_form.html',$messages);


 if ( !$p->ajax ) $p->HTML('footer.html');
 $p->Render();

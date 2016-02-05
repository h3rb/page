<?php

 include 'core/Page.php';



 $getpost = getpost();
 $getpost['m']=intval($getpost['m']);

 $p=new Page();

 if ( isset($getpost['m']) && intval($getpost['m']) == 1 ) {
 }

 $p->Render();

?>

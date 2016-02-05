<?php

 include 'core/Page.php';

 echo 'woot! '; var_dump(getpost());

 foreach ( getpost() as $name=>$value ) {
  echo '<hr>'.$name.'<br>'.urldecode(base64_decode($value));
 }

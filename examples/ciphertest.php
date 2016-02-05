<?php

 include '../core/Page.php';


 $c=new Cipher('aabb');
 echo $c->encrypt('Poopie');
 echo '<HR>';
 echo $c->decrypt($c->encrypt('Poopie'));

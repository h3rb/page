<!DOCTYPE html>
<?php
 include 'FontSpriter.php';

?>
<html>
<head>
 <title>FontLawyer: Use your fonts free and clear!</title>
 <?php echo fl_get_sheet( "SheetID" ) ?>
</head>
<body>
<?php

 echo fl_sprite( __FILE__, "SheetID", "SpriteID" );
 echo fl_sprite( __FILE__, "SheetID", "one" );
 echo fl_sprite( __FILE__, "SheetID", "two" );

 include 'Render.php';

 echo fl_font( __FILE__, "TestingID", "Some Text!", "MyriadPro-Regular.ttf", 72 );

?>
</body>
</html>

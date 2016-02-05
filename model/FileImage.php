<?php // Table: FileImage

 class FileImage extends Model {

  public function byFile( $ref ) {
   return array_pop($this->Select( array( 'r_File'=>$ref ) ));
  }
  public function GetFile( $fi ) {
   global $database;
   $fm=new File($database);
   return $fm->Get($fi['r_File']);
  }
  public function ThumbName( $fi, $size=100 ) {
   $f=$this->GetFile($fi);
   return 'thumb.php?wh='.$size.'&src='.urlencode('cache/files/'.$fi['r_File'].'.'.$f['Extension']);
  }
 };

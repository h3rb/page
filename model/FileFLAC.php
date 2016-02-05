<?php // Table: FileSTL

 class FileFLAC extends Model {
  public function byFile( $ref ) {
   return array_pop($this->Select( array( 'r_File'=>$ref ) ));
  }
 };

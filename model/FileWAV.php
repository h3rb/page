<?php // Table: FileWAV

 class FileWAV extends Model {
  public function byFile( $ref ) {
   return array_pop($this->Select( array( 'r_File'=>$ref ) ));
  }
 };

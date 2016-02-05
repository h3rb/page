<?php // Table: File

 class File extends Model {

  public function byCreator( $id ) {
   return $this->Select( array( 'Uploader'=>$id ) );
  }

  public function Latest( $id, $byType=FALSE, $start=0, $limit=1000 ) {
   $ordering=' ORDER BY ID DESC LIMIT '.$start.','.$limit;
   if ( $byType === FALSE )
   return $this->Select( 'Uploader = '.$id.$ordering);
   if ( matches($byType, 'stl') ) // All models viewable by everyone
   return $this->Select( 'Type LIKE "%stl%"'.$ordering);
   else if ( matches($byType,'images') )
   return $this->Select( 'Type LIKE "image%"'.$ordering);
   else if ( matches($byType,'configs') )
   return $this->Select( 'Type LIKE "text/plain"'.$ordering);
   else if ( matches($byType,'wav') )
   return $this->Select( 'Type LIKE "audio/x-wav"'.$ordering);
   else if ( matches($byType,'flac') )
   return $this->Select( 'Type LIKE "audio/flac"'.$ordering);
   else if ( matches($byType,'f3xb') )
   return $this->Select( 'Type LIKE "font/3d-extruded-binary"'.$ordering);
   else
   return $this->Select( 'ORDER BY ID DESC');
  }

  public function LatestTotals( $id, $byType=FALSE ) {
   $ordering=' ';
   if ( $byType === FALSE )
   $result=$this->Select( 'Uploader = '.$id.$ordering);
   else if ( matches($byType, 'stl') )
   $result=$this->Select( 'Type LIKE "%stl%"'.$ordering);
   else if ( matches($byType,'images') )
   $result=$this->Select( 'Type LIKE "image%"'.$ordering);
   else if ( matches($byType,'configs') )
   $result=$this->Select( 'Type LIKE "text/plain"'.$ordering);
   else if ( matches($byType,'wav') )
   $result=$this->Select( 'Type LIKE "audio/wav"'.$ordering);
   else if ( matches($byType,'flac') )
   $result=$this->Select( 'Type LIKE "audio/flac"'.$ordering);
   else if ( matches($byType,'f3xb') )
   $result=$this->Select( 'Type LIKE "font/3d-extruded-binary"'.$ordering);
   else
   $result=$this->Select( 'ORDER BY ID DESC');
   return false_or_null($result) ? 0 : count($result);
  }

  // TODO: Implement a good test
  public function inUse( $row ) {
   if ( contains($row['Type'],'stl') ) {
   } else if ( contains($row['Type'],'image') ) {
   } else if ( contains($row['Type'],'text/plain') ) {
   } else if ( contains($row['Type'],'wav') ) {
   }
   return FALSE;
  }

  public function StoredName( $f ) {
   return 'cache/files/'.$f['ID'].'.'.$f['Extension'];
  }

  public function ThumbName( $f ) {
   return 'thumb.php?src='.urlencode('cache/files/'.$f['ID'].'.'.$f['Extension']);
  }
 };

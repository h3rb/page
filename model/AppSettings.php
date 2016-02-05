<?php // Table: AppSettings

 class AppSettings extends Model {
  public function Latest() {
   $settings=$this->Select(' ORDER BY ID LIMIT 1 DESC');
   if ( false_or_null($settings) ) { // Defaults
    return array(
    );
   } else return json_decode($settings['JSON'],true);
  }
  public function Save( $arr ) {
   return $this->Insert( array( 'JSON'=>json_encode($arr) ) );
  }
 };

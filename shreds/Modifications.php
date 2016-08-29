<?php

 function RemoveOldModifications() {
  global $database;
  $m=new Modification($database);
  $m->Delete('Timestamp < '.strtotime("-7 days"));
 }

 // $what should come in the form of:
 //  array(
 //   "D"=>array( "T"=>array("fields"), "I"=>value ),
 //  );
 // Message is a phrase with markdown that can be added to a normal data modification message.
 // In some cases, Message's first word can be literally 'JSON' followed by encoded json,
 // that has a recognizable format starting with array( "mode"=>XYZ )
 function Modified( $what, $message='' ) {
//  plog('Modified('.$what.','.$message.')');
  global $auth,$database;
  $m=new Modification($database);
  $mods=$m->Select('r_Auth = '.$auth['ID'].' ORDER BY Timestamp DESC LIMIT 50');
  $now=strtotime('now');
  $what_json=json_encode($what);
  // Messages are not saved if they are a duplicate of a recent event.
  if ( !false_or_null($mods) ) {
   foreach ( $mods as $a ) {
    if ( $now-intval($a['Timestamp']) > 30 ) continue;
    if ( strlen($a['What']) === strlen($what_json)
       && matches($what_json,$a['What']) && matches($message,$a['Message'])
     ) { /*plog("Modified matched previous message");*/ return FALSE; }
   }
  }
  RemoveOldModifications();
  return $m->Insert( array(
   'r_Auth'=>$auth['ID'],
   'What'=>$what_json,
   'Message'=>$message,
   'Timestamp'=>$now
  ) );
 }


 function ShowModifications( &$p, $db_table, $db_id ) {
  global $database;
  $m_mod=new Modification($database);
  $m_auth=new Auth($database);
  $mods=$m_mod->byTableID($db_table,$db_id);
  if ( !false_or_null($mods) ) {
   $data=array();
   foreach ( $mods as $mod ) {
    $user=$m_auth->Get($mod['r_Auth']);
    $what='';
    $w=json_decode($mod['What'],true);
    foreach ( $w as $dataset ) {
     foreach ( $dataset as $table=>$change ) {
      $what.=$table.' #'.$change['I'].'\'s '.$change['F']. ( isset($change['E']) ? ' &raquo; '.$change['E'] : '' );
     }
    }
    $data[]=array(
      $user['username'],
      $what,
      $mod['Message'],
      human_datetime(intval($mod['Timestamp'])),
    );
   }

   $table=new TableHelper( array(
    'table'=>"table wide",
    'thead'=>"tablehead",
    'th'=>"tablehead",
    'td'=>"tablecell",
    'headings'=>array(
      'Who',
      'What',
      '&nbsp;',
//     'Message',
      'When'
     ),
     'data'=>$data
    )
   );

   $p->HTML('<div class="formgroup">');
   $p->HTML('<h4>Recent Activity</h4>');
   $p->Table($table);
   $p->HTML('</div>');
  }

 }

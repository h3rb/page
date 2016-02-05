<?php

// global $plog_level; $plog_level=1;
 include 'core/Page.php';

 if ( !Session::logged_in() ) Page::Redirect('login');

 plog("New page!");
 $p= new Page;

 if ( !$p->ajax ) $p->HTML('header.html',array("###MENU###"=>Dropdown_menu($p)));

 $p->title ="Your Website";
 $p->CSS( "main.css" );
 $p->Jquery();

 $getpost=getpost();

 $p->HTML('<BR>');

 global $auth_database;

 plog("Get mods!");

 $m=new Modification($auth_database);
 $mods=$m->All('ORDER BY Timestamp DESC');

 $m_auth=new Auth($auth_database);

 if ( !false_or_null($mods) ) {

  $datatemp=array();

  foreach ( $mods as $mod ) {
   $user=$m_auth->Get($mod['r_Auth']);
   if ( !isset($datatemp[$user['username']]) ) $datatemp[$user['username']]=1;
   else $datatemp[$user['username']]+=1;
  }

  $datapoints=array();
  foreach ( $datatemp as $username=>$events ) {
   $datapoints[]=array( "value"=>$events, "color"=>"#FFAAAA", "highlight"=>"5AD3D1", "label"=>$username );
  }

  $chart=Pie($p,400,400,$datapoints);
//  $p->CSS('.'.$chart.' { }');

  $data=array();
  foreach ( $mods as $mod ) {
   $user=$m_auth->Get($mod['r_Auth']);
   $what='';
   $w=json_decode($mod['What'],true);
   foreach ( $w as $dataset ) {
    foreach ( $dataset as $table=>$change ) {
     $what.=$table.' <a class="bare" href="'.strtolower($table).'.edit?ID='.$change['I'].'" title="Edit '.$table.' #'.$change['I'].'">#'.$change['I'].'</a>\'s '
           .$change['F']. ( isset($change['E']) ? ' &raquo; '.$change['E'] : '' );;
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

  $p->HTML('<h3>Activity Since '.date('M-d-Y',strtotime("-6 months")).'</h3>');

  $p->HTML('<div class="formboundary">');
  $p->Table($table);
  $p->HTML('</div>');
 }

 if ( !$p->ajax ) $p->HTML('footer.html');
 $p->Render();


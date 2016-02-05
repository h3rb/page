<?php

 global $plog_level; $plog_level=1;
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

 $p->HTML('groundrules.html');

///// Show recent activity

 // Show edit activity

 global $auth_database;

 plog("Get mods!");

 $m=new Modification($auth_database);
 $mods=$m->Select(' Timestamp > '.strtotime('-1 days').' ORDER BY Timestamp DESC');

 $m_auth=new Auth($auth_database);

 if ( !false_or_null($mods) ) {
  $data=array();
  foreach ( $mods as $mod ) {
   $user=$m_auth->Get($mod['r_Auth']);
   $what='';
   $w=json_decode($mod['What'],true);
   foreach ( $w as $dataset ) {
    foreach ( $dataset as $table=>$change ) {
     $edit=strtolower($table).".edit";
     if ( $edit == "catalogcategory.edit" ) $edit="category.edit";
     else if ( $edit == "catalogexport.edit" ) $edit="export.inspect";
     $what.=$table.' <a class="bare" href="'.$edit.'?ID='.$change['I'].'" title="Edit '.$table.' #'.$change['I'].'">#'.$change['I'].'</a>\'s '
           .$change['F']. ( isset($change['E']) ? ' &raquo; '.$change['E'] : '' );
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

  $p->HTML('<h3>Edit activity since yesterday</h3>');

  $p->HTML('<div class="formhighlight"><center>');
  $p->Table($table);
  $p->HTML('</center></div>');

 }
 $p->HTML('<div class="addbtn"><a href="edit.log" class="buttonlink bare"><span class="fi-magnifying-glass"></span> See edit log</a></div>');

 if ( !$p->ajax ) $p->HTML('footer.html');
 $p->Render();


<?php

 include 'core/Page.php';

 if ( !Session::logged_in() ) Page::Redirect('login');

 $p= new Page;
 if ( !$p->ajax ) $p->HTML('header.html',array("###MENU###"=>Dropdown_menu($p)));

 $p->title ="Your Website";
 $p->CSS( "main.css" );
 $p->Jquery();

 $html='';

 $getpost=getpost();
 if ( isset($getpost['new']) ) {
  $p->JS('notifier.js');
  $p->JS('Notifier.info("New User Account Created with Empty Password");');
 }

 $p->HTML('<BR>');

 if ( isset($getpost['nosuchpost']) ) {
  $p->JS('notify.js');
  $p->KS('Notifier.warning("Bad form request.");');
 }

 global $auth_database, $auth;

 $getpost=getpost();

 $page_limit=50;
 if ( !isset($getpost['start']) ) $getpost['start']=0;

 $profile_model=new Profile($auth_database);
 $auth_model=new Auth($auth_database);

 $profiles=$profile_model->All();

 global $database;

 $data=array();
 foreach ( $profiles as $pro ) {
  $a=$auth_model->byProfile($pro['ID']);
  $link_title="Send mail to ".$pro['first_name'].' '.$pro['last_name'].' or right click to copy URL\'s email address';
  $access='';
  $acls=explode(",",$a['acl']);
  foreach ( $acls as $ac ) if ( strlen($level=trim($ac))>0 ) $access.='<span class="acl">'.$ac.'</span>';
  $data[]=array(
   $a['username'],
   $pro['first_name'].' '.$pro['last_name'],
   '<a href="mailto:'.$pro['email'].'" title="'.$link_title.'" alt="'.$link_title.'" class="bare">'.$pro['email'].'</a>',
   $access,
   (Auth::ACL("admin")||Auth::ACL("su")?'<a class="buttonsmred margined" title="Edit User: '.$a['username'].'" href="profile.edit?ID='.$a['ID'].'"><span class="fi-page-edit"></span></a>':'')
  );
 }

 $table=new TableHelper( array(
   'table'=>"table wide",
   'thead'=>"tablehead",
   'th'=>"tablehead",
   'td'=>"tablecell",
   'headings'=>array(
    'Username',
    'Name',
    'Email',
    'Access',
    '&nbsp;'
   ),
   'data'=>$data
  )
 );

 $table->Render($table);

 $html='<span class="breadcrumbs">Leaderboard</span>';
 $html.='<div class="formboundary">';
 $html.=$table;
 $html.='</div>';

 $p->HTML($html);

 $counted=count($profile_model->All());
 if ( $counted >= $page_limit ) Pager($p,"profiles?",$getpost['start'],$page_limit,$counted);

 $p->HTML('<a class="buttonlink" href="profile.new">+ Add User Account</a>');

// Footer
 if ( !$p->ajax ) $p->HTML('footer.html');
 $p->Render();



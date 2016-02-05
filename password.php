<?php

 include 'core/Page.php';

 if ( !Session::logged_in() ) Page::Redirect('login');

 $df=new DataForm( 'changeMyPassword.txt' );
 $df->form->Prepare();

 $p = new Page();
 if ( !$p->ajax ) {
  $p->HTML('header.html');
  $p->title='Form Test';
  $p->CSS( 'main.css' );
  $p->Jquery();
  $p->JS( CDN_JQ_CHOSEN );
  $p->CSS( CDN_JQ_CHOSEN_CSS );
 }

 $p->Add(new DataForm('changeMyPassword.txt'));

 if ( !$p->ajax ) $p->HTML('footer.html');
 $p->Render();

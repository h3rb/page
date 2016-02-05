<?php // Standard AJAX post for DataForm and FormHelper shreds

// global $plog_level; $plog_level=1;
 include 'core/Page.php';

 $ajax=AJAX::FormPost();

 plog('--- $ajax: '.vars($ajax) );
// var_dump($ajax); die;

 if ( !isset($ajax['map']) ) { echo 'AJAX error!'; die; }

 $post_types=array(
  1=>'changeMyPassword'
 );

 $modes=array();
 foreach ( $ajax['map'] as $form=>$elements ) {
  $mode=matchvalue($post_types,$form);
  if ( $mode !== FALSE ) $modes[]=$mode;
 }

 plog('--- detected ajax modes '.vars($modes) );
 global $database;

 foreach ( $modes as $mode ) switch ( $mode ) {

  default: Page::Redirect('dash?nosuchform'); break;

  case 1: // changeMyPassword
   {
    if ( !Session::logged_in() ) Page::Redirect('login');
    global $auth;
    $old=AJAX::Value($ajax,'changeMyPassword','password','old');
    $change=AJAX::Value($ajax,'changeMyPassword','password','new');
    $repeat=AJAX::Value($ajax,'changeMyPassword','password','confirm');
    if ( strlen($auth['password'])===0
      || Auth::PasswordMatches(ourcrypt($old),$auth['password']) ) {
     if ( matches($change,$repeat,TRUE) ) {
      global $auth_model;
      $auth_model->Update( array(
         'password' => ourcrypt($change),
         'password_expiry' => strtotime('+1 year')
        ),
        array( 'ID'=>$auth['ID'] ) );
      echo js( 'Notifier.success("Password changed!");' );
      die;
     } else {
      echo js( 'Notifier.error("Passwords did not match.");' );
      die;
     }
    } else {
     echo js( 'Notifier.error("You got your password wrong.","Logging you out.");
               setTimeout( function() { window.location="logout"; }, 2000 );' );
     die;
    }
   }
  break;

 } // end switch

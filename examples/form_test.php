<?php

 include '../core/Page.php';

 $f=new FormHelper( "myForm", "http://dm.3druck.us/examples/ajax_form.php" );
 $f->Add( new FormSelect( array(
    'field'=>'First.Name',
    'placeholder'=>'Hello World...',
    'html'=>array(
     'class'=>'chosen-select',
     'style'=>'width:50%'
    ),
    'options'=>array(
     0=>array( 'name'=>'Pooty', 'value'=>'Tang' ),
     1=>array( 'name'=>'Elvira', 'value'=>'Dark Mistress' ),
    ),
    'disable_search'=>true
   )
  )
 );
 $f->Add( new FormRadio( array(
    'field'=>'Last.Name',
    'options'=>array(
     0=>array( 'label'=>'poots', 'name'=>'Pooty', 'value'=>'Tang' ),
     1=>array( 'label'=>'elvis', 'name'=>'Elvira', 'value'=>'Dark Mistress' ),
    )
   )
  )
 );
 $f->Add( new FormSlider( array(
    'field'=>'Volume.Control',
   )
  )
 );
 $f->Add( new FormText( array(
    'field'=>'BoopText',
    'value'=>'boop'
   )
  )
 );
 $f->Add( new FormSubmit( array(
    'value'=>'Submitty this formy'
   )
  )
 );
 $f->Prepare();

 //var_dump($f);

 $p = new Page();

 $p->title='Form Test';
 $p->JS( CDN_JQUERY_LATEST );
 $p->JS( CDN_JQUERY_UI  );
 $p->JS( CDN_JQ_CHOSEN );
 $p->CSS( CDN_JQUERY_UI_CSS );
 $p->CSS( CDN_JQ_CHOSEN_CSS );

 $p->JQ( $f->jq, TRUE );
 $p->HTML( $f->html );

 $p->Render();

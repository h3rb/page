<?php

 // Loads a file from the forms/ folder, implements the form,
 // prepopulates it if $data is provided
 /// $addendum are an array of settings added to $settings, such as ID
 class DataForm {
  var $data,$dbid,$form;
  public function __construct( $formfile, $data=NULL, $addendum=NULL, $signal=NULL ) {
   $this->dbid=NULL;
   $this->data=$data;
   $this->Load($formfile,$addendum,$signal);
  }
  public function Load($ff,$addendum,$signal) {
   if ( !isfile($ff,'forms/') ) return;
   $file=file_get_contents('forms/'.$ff);
   $data=new HDataStream($file);
   $data=$data->toArray();
   $settings=array();
   $settings['insert']=array();
   $settings['hidden']=array();
   $settings['text']=array();
   $settings['multiline']=array();
   $settings['slider']=array();
   $settings['date']=array();
   $settings['select']=array();
   $settings['radio']=array();
   $settings['submit']=array();
   $settings['list']=array();
   $settings['name']='form';
   $settings['action']='ajax.post.php';
   if ( !is_null($addendum) && is_array($addendum) ) foreach ( $addendum as $add=>$val ) $settings[$add]=$val;
   if ( isset($settings['dbid']) ) $this->dbid=$settings['dbid'];
   $t=count($data);
   $o=0;
   for ( $i=0; $i<$t; $i++ ) {
    $setting=$data[$i+1];
    if ( matches($data[$i],"text") ) {
     $settings['text'][$o++]=HDataStream::asKV($setting);
    } else if ( matches($data[$i],"hidden") ) {
     $settings['hidden'][$o++]=HDataStream::asKV($setting);
    } else if ( matches($data[$i],"multiline") || matches($data[$i],"textarea") ) {
     $settings['multiline'][$o++]=HDataStream::asKV($setting);
    } else if ( matches($data[$i],"slider") ) {
     $settings['slider'][$o++]=HDataStream::asKV($setting);
    } else if ( matches($data[$i],"date" ) ) {
     $settings['date'][$o++]=HDataStream::asKV($setting);
    } else if ( matches($data[$i],"select" ) ) {
     $settings['select'][$o++]=HDataStream::asKV($setting);
    } else if ( matches($data[$i],"list" ) ) {
     $settings['list'][$o++]=HDataStream::asKV($setting);
    } else if ( matches($data[$i],"radio" ) ) {
     $settings['radio'][$o++]=HDataStream::asKV($setting);
    } else if ( matches($data[$i],"insert" ) ) {
     $settings['insert'][$o++]=HDataStream::asKV($setting);
    } else if ( matches($data[$i],"submit") ) {
     $settings['submit'][$o++]=HDataStream::asKV($setting);
    } else {
     $settings[$data[$i]]=$setting;
    }
    $i++;
   }
   $this->form=new FormHelper( $settings['name'], $settings['action'], $this->dbid, $signal );
   for ( $i=0; $i<$o; $i++ ) {
    if ( isset($settings['radio'][$i]) ) {
     $e=$settings['radio'][$i];
     $e['html']=HDataStream::asKV($e['html']);
     $e['options']=HDataStream::asKV($e['options']);
     $element=new FormRadio($e,TRUE);
     if ( !is_null($this->data) && isset($e['data']) && isset($this->data[$e['data']]) ) {
      $element->Set($this->data[$e['data']]);
     }
     $element->_Init($element->settings);
     $this->form->Add( $element );
    } else if ( isset($settings['select'][$i]) ) {
     $e=$settings['select'][$i];
     $e['html']=HDataStream::asKV($e['html']);
     $e['options']=HDataStream::asKV($e['options']);
     $element=new FormSelect($e,TRUE);
     if ( !is_null($this->data) && isset($e['data']) && isset($this->data[$e['data']]) ) {
      $element->Set($this->data[$e['data']]);
     }
     $element->_Init($element->settings);
     $this->form->Add( $element );
    } else if ( isset($settings['list'][$i]) ) {
     $e=$settings['list'][$i];
     $e['html']=HDataStream::asKV($e['html']);
     $e['options']=HDataStream::asKV($e['options']);
     $element=new FormList($e,TRUE);
     if ( !is_null($this->data) && isset($e['data']) && isset($this->data[$e['data']]) ) {
      $element->Set($this->data[$e['data']]);
     }
     $element->_Init($element->settings);
     $this->form->Add( $element );
    } else if ( isset($settings['slider'][$i]) ) {
     $e=$settings['slider'][$i];
     $e['html']=HDataStream::asKV($e['html']);
     $element=new FormSlider($e,TRUE);
     if ( !is_null($this->data) && isset($e['data']) && isset($this->data[$e['data']]) ) {
      $element->Set($this->data[$e['data']]);
     }
     $element->_Init($element->settings);
     $this->form->Add( $element );
    } else if ( isset($settings['multiline'][$i]) ) {
     $e=$settings['multiline'][$i];
     $e['html']=HDataStream::asKV($e['html']);
     $element=new FormMultiline($e,TRUE);
     if ( !is_null($this->data) && isset($e['data']) && isset($this->data[$e['data']]) ) {
      $element->Set($this->data[$e['data']]);
     }
     $element->_Init($element->settings);
     $this->form->Add( $element );
    } else if ( isset($settings['text'][$i]) ) {
     $e=$settings['text'][$i];
     $e['html']=HDataStream::asKV($e['html']);
     $element=new FormText($e,TRUE);
     if ( !is_null($this->data) && isset($e['data']) && isset($this->data[$e['data']]) ) {
      $element->Set($this->data[$e['data']]);
     }
     $element->_Init($element->settings);
     $this->form->Add( $element );
    } else if ( isset($settings['insert'][$i]) ) {
     $e=$settings['insert'][$i];
     $this->form->Add( new FormInsert($e) );
    } else if ( isset($settings['hidden'][$i]) ) {
     $e=$settings['insert'][$i];
     $this->form->Add( new FormHidden($e) );
    } else if ( isset($settings['submit'][$i]) ) {
     $e=$settings['submit'][$i];
     $e['html']=HDataStream::asKV($e['html']);
     $element=new FormSubmit($e,TRUE);
     if ( !is_null($this->data) && isset($e['data']) && isset($this->data[$e['data']]) ) {
      $element->Set($this->data[$e['data']]);
     }
     $element->_Init($element->settings);
     $this->form->Add( $element );
    }
   }
  }
 };

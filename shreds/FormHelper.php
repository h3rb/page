<?php

 class FormElement extends Unique {
  var $uid,$name,$id,$db,$field,$settings,$tag;
  var $jq,$js,$html;
  public function __construct( $settings, $defer=FALSE ) {
   $this->jq='';
   $this->js='';
   $this->html='';
   $this->uid=$this->Uniqueness();
   $this->name=get_class($this).$this->uid;
   $this->settings=$settings;
   if ( $defer === FALSE ) $this->_Init($settings);
  }
  function _Init( $settings ) {
   if ( $this->s('field') ) $this->field=$settings['field'];
   $this->Init($settings);
  }
  protected function Init( $settings ) {}
  public function Set($value) {}
  public function s($name) { return isset($this->settings[$name]); }
  public function so($a,&$b,$name) { if ( isset($a[$name]) ) $b[$name]=$a[$name]; }
  public function a(&$tag_arr,$name) { if ( $this->s($name) ) $tag_arr[$name]=$this->settings[$name]; }
  public function j($json_arr) { $this->jq.=$this->je($json_arr); }
  public function je($json_arr) {
   $out=""; $total=count($json_arr);
   foreach ( $json_arr as $v=>$val ) {
    $total--;
    if ( is_array($val) )   $value='{'.$this->je($val).'}';
    else if ( is_null($val) ) $value='null';
    else if ( is_string($val) )  $value='"'.$val.'"';
    else if ( is_bool($val) ) $value=$val?'true':'false';
    else $value=$val;
    $out.=(!is_numeric($v) && !is_integer($v) ? ($v.':') : '').$value.($total>0?',':'');
   }
   return $out;
  }
  public function o(&$json_arr,$name) { if ( $this->s($name) ) $json_arr[$name]=$this->settings[$name]; }
  public function t($tagname,$tagarray,$interior=FALSE,$closetag=FALSE ) {
   $tag='<'.$tagname;
   foreach ( $tagarray as $e=>$v ) {
    if ( is_bool($v) ) $tag.=' '.$e;
    else $tag.=' '.$e.'="'.$v.'"';
   }
   $tag.='>';
   if ( $interior !== FALSE ) $tag.=$interior;
   if ( $closetag !== FALSE ) $tag.='</'.$tagname.'>';
   return $tag;
  }
  public function Data() {
   return '$("#'.$this->name.'").val()';
  }
 };

 class FormInsert extends FormElement {
  var $element_replacement_key;
  function Init( $settings ) {
   if ( $this->s('html') ) $this->html=$settings['html'];
   if ( $this->s('element') ) $this->element_replacement_key=$settings['element'];
  }
  function Data() { return FALSE; }
 };

 class FormHidden extends FormElement {
  var $name,$value;
  function Init( $settings ) {
   $tag=($this->s("html") && is_array($settings['html']) ? $settings['html'] : array());
   $tag['id']    =$this->name;
   $tag['name']  =$this->s('name')  ? $settings['name']  : $this->name;
   $tag['value'] =$this->s('value') ? $settings['value'] : $this->value;
   $tag['type']  ='hidden';
   $this->html=$this->t('input',$tag);
  }
  public function Data() {
   return '$("#'.$this->name.'").val()';
  }
 };

 class FormSubmit extends FormElement {
  function Init( $settings ) {
   $tag=($this->s("html") && is_array($settings['html']) ? $settings['html'] : array());
   $tag['id']=$this->name;
   $tag['name']=$this->s('name') ? $settings['name'] : $this->name;
   if ( $this->s('value') ) $tag['value']=$settings['value']; else $tag['value']="Save";
   $tag['type']="button";
   $this->html=$this->t('input',$tag);
  }
  function Data() { return FALSE; }
 };

 class FormSelect extends FormElement {
  function Init( $settings ) {
   $tag=($this->s("html") && is_array($settings['html']) ? $settings['html'] : array());
   $tag['id']=$this->name;
   $tag['name']=$this->s('name') ? $settings['name'] : $this->name;
   if ( $this->s('placeholder') ) $tag['data-placeholder']=$settings['placeholder'];
   if ( $this->s('multiple') ) $tag['multiple']=TRUE;
   $this->html=$this->t('select',$tag);
   foreach ( $settings['options'] as $numbered=>$option ) {
    $opt=array();
    if ( !is_array($option) ) {
     plog('FormSelect::Error','$option was not an array');
    } else {
     $this->so($option,$opt,'disabled');
     $this->so($option,$opt,'label');
     $this->so($option,$opt,'selected');
     $this->so($option,$opt,'value');
    }
    if ( count($opt) > 0 ) $this->html.=$this->t('option',$opt,$option['name'],TRUE);
   }
   $this->html.='</select>';
   $this->jq.='$("#'.$this->name.'").chosen({';
   $json=array();
   $this->o($json,'allow_single_deselect');
   $this->o($json,'disable_search');
   $this->o($json,'disable_search_threshold');
   $this->o($json,'enable_split_word_search');
   $this->o($json,'inherit_select_classes');
   $this->o($json,'max_selected_options');
   $this->o($json,'no_results_text');
   $this->o($json,'placeholder_text_single');
   $this->o($json,'placeholder_text_multiple');
   $this->o($json,'search_contains');
   $this->o($json,'single_backstroke_delete');
   $this->o($json,'display_disabled_options');
   $this->o($json,'display_selected_options');
   $this->o($json,'include_group_label_in_selected');
   $this->o($json,'width');
   $this->j($json);
   $this->jq.='});'.PHP_EOL;
  }
  function Set($value) {
  }
 };


 class FormList extends FormElement {
  function Init( $settings ) {
   $tag=($this->s("html") && is_array($settings['html']) ? $settings['html'] : array());
   $tag['id']=$this->name;
   $tag['name']=$this->s('name') ? $settings['name'] : $this->name;
   $tag['size']=$this->s('size') ? $settings['size'] : 4;
   if ( $this->s('multiple') ) $tag['multiple']=TRUE;
   $this->html=$this->t('select',$tag);
   foreach ( $settings['options'] as $numbered=>$option ) {
    $opt=array();
    if ( !is_array($option) ) {
     plog('FormList::Error','$option was not an array');
    } else {
     $this->so($option,$opt,'disabled');
     $this->so($option,$opt,'label');
     $this->so($option,$opt,'selected');
     $this->so($option,$opt,'value');
    }
    if ( count($opt) > 0 ) $this->html.=$this->t('option',$opt,$option['name'],TRUE);
   }
   $this->html.='</select>';
  }
  function Set($value) {
  }
 };



 class FormFile extends FormElement {
  function Init( $settings ) {
  }
 };

 class FormSlider extends FormElement {
  function Init( $settings ) {
   $tag=($this->s("html") && is_array($settings['html']) ? $settings['html'] : array());
   $tag['id']=$this->name;
   $tag['name']=$this->s('name') ? $settings['name'] : $this->name;
   if ( $this->s('value') ) $tag['value']=$settings['value'];
   $this->html=$this->t('div',$tag,FALSE,TRUE);
   $this->jq.='$("#'.$this->name.'").slider({';
   $json=array();
   $this->o($json,'animate');
   $this->o($json,'disabled');
   $this->o($json,'max');
   $this->o($json,'min');
   $this->o($json,'orientation');
   $this->o($json,'range');
   $this->o($json,'step');
   $this->o($json,'value');
   $this->o($json,'values');
   $this->j($json);
   $this->jq.='});'.PHP_EOL;
  }
  public function Data() {
   return '$("#'.$this->name.'").slider("value")';
  }
 };

 class FormSpinner extends FormElement {
  function Init( $settings ) {
   $tag=($this->s("html") && is_array($settings['html']) ? $settings['html'] : array());
   $tag['id']=$this->name;
   $tag['name']=$this->s('name') ? $settings['name'] : $this->name;
   if ( $this->s('value') ) $tag['value']=$settings['value'];
   $this->html=$this->t('input',$tag);
   $this->jq.='$("#'.$this->name.'").spinner({';
   $json=array();
   $this->o($json,'culture');
   $this->o($json,'disabled');
   $this->o($json,'icons');
   $this->o($json,'incremental');
   $this->o($json,'max');
   $this->o($json,'min');
   $this->o($json,'numberFormat');
   $this->o($json,'page');
   $this->o($json,'step');
   $this->j($json);
   $this->jq.='});'.PHP_EOL;
  }
 };

 class FormDate extends FormElement {
  function Init( $settings ) {
   $tag=($this->s("html") && is_array($settings['html']) ? $settings['html'] : array());
   $tag['id']=$this->name;
   $tag['name']=$this->s('name') ? $settings['name'] : $this->name;
   if ( $this->s('placeholder') ) $tag['placeholder']=$settings['placeholder'];
   if ( $this->s('value') ) $tag['value']=$settings['value'];
   $this->html=$this->t('input',$tag);
   $this->jq.='$("#'.$this->name.'").datepicker({';
   $json=array();
   $this->o($json,'altField');
   $this->o($json,'altFormat');
   $this->o($json,'appendText');
   $this->o($json,'autoSize');
   $this->o($json,'beforeShow');
   $this->o($json,'beforeShowDay');
   $this->o($json,'buttonImage');
   $this->o($json,'buttonImageOnly');
   $this->o($json,'buttonText');
   $this->o($json,'calculateWeek');
   $this->o($json,'changeMonth');
   $this->o($json,'changeYear');
   $this->o($json,'closeText');
   $this->o($json,'constrainInput');
   $this->o($json,'currentText');
   $this->o($json,'dateFormat');
   $this->o($json,'dayNames');
   $this->o($json,'dayNamesMin');
   $this->o($json,'dayNamesShort');
   $this->o($json,'defaultDate');
   $this->o($json,'duration');
   $this->o($json,'firstDay');
   $this->o($json,'gotoCurrent');
   $this->o($json,'hideIfNoPrevNext');
   $this->o($json,'isRTL');
   $this->o($json,'maxDate');
   $this->o($json,'minDate');
   $this->o($json,'monthNames');
   $this->o($json,'monthNamesShort');
   $this->o($json,'navigationAsDateFormat');
   $this->o($json,'nextText');
   $this->o($json,'numberOfMonths');
   $this->o($json,'onChangeMonthYear');
   $this->o($json,'onClose');
   $this->o($json,'onSelect');
   $this->o($json,'prevText');
   $this->o($json,'selectOtherMonths');
   $this->o($json,'shortYearCutoff');
   $this->o($json,'showAnim');
   $this->o($json,'showButtonPanel');
   $this->o($json,'showCurrentAtPos');
   $this->o($json,'showMonthAfterYear');
   $this->o($json,'showOn');
   $this->o($json,'showOptions');
   $this->o($json,'showOtherMonths');
   $this->o($json,'showWeek');
   $this->o($json,'stepMonths');
   $this->o($json,'weekHeader');
   $this->o($json,'yearRange');
   $this->o($json,'yearSuffix');
   $this->j($json);
   $this->jq.='});'.PHP_EOL;
  }
 };

 class FormText extends FormElement {
  function Init( $settings ) {
   $tag=($this->s("html") && is_array($settings['html']) ? $settings['html'] : array());
   $tag['id']=$this->name;
   $tag['name']=$this->s('name') ? $settings['name'] : $this->name;
   $tag['type']=isset($settings['password'])?"password":"text";
   if ( $this->s('placeholder') ) $tag['placeholder']=$settings['placeholder'];
   if ( $this->s('value') ) $tag['value']=$settings['value'];
   $this->html=$this->t('input',$tag);
  }
  public function Set($value) {
   $this->settings['value']=$value;
  }
 };

 class FormMultiline extends FormElement {
  function Init( $settings ) {
   $tag=($this->s("html") && is_array($settings['html']) ? $settings['html'] : array());
   $tag['id']=$this->name;
   $tag['name']=$this->s('name') ? $settings['name'] : $this->name;
   $tag['type']=isset($settings['password'])?"password":"text";
   if ( $this->s('placeholder') ) $tag['placeholder']=$settings['placeholder'];
   $this->html=$this->t('textarea',$tag,$this->s('value')?$settings['value']:FALSE,TRUE);
  }
  public function Set($value) {
   $this->settings['value']=$value;
  }
 };

 class FormRadio extends FormElement {
  function Init( $settings ) {
   $tag=($this->s("html") && is_array($settings['html']) ? $settings['html'] : array());
   $tag['id']=$this->name;
   $tag['name']=$this->s('name') ? $settings['name'] : $this->name;
   $this->html=$this->t('div',$tag);
   if ( $this->s('dbid') ) $this->dbid=$settings['dbid'];
   $o=0;
   foreach ( $settings['options'] as $numbered=>$option ) {
    $opt=array();
    $opt['type']="radio";
    $opt['name']=$this->name.'_option';
    $opt['id']=$this->name.'_'.$o; $o++;
    if ( !is_array($option) ) {
     plog('FormRadio::Error','$option was not an array');
    } else {
     $this->so($option,$opt,'disabled');
     if ( isset($option['selected']) ) $opt['checked']="checked";
     $this->so($option,$opt,'value');
    }
    if ( count($opt) > 0 ) $this->html.=$this->t('input',$opt);
    if ( isset($option['label']) ) {
     $this->html.='<label for="'.$opt['id'].'">'.$option['label'].'</label>';
    }
   }
   $this->html.='</div>';
   $this->jq='$("#'.$this->name.'").buttonset({';
   $json=array();
   $this->o($json,'disabled');
   $this->o($json,'items');
   $this->j($json);
   $this->jq.='});'.PHP_EOL;
  }
  public function Data() {
   return '$("#'.$this->name.' :radio:checked").attr("id")';
  }
 };

 class FormHelper extends Unique {
  var $url,$uid,$id,$rap,$map,$name,$element,$js,$jq,$html,$table,$dbid,$signal;
  public function __construct( $rap="form", $url="ajax.post.php", $dbid=NULL, $signal=NULL ) {
   $this->url=$url;
   $this->db=$db;
   $this->element=array();
   $this->rap=$rap;
   $this->map=array();
   $this->uid=$this->Uniqueness();
   $this->name=$rap.get_class($this).$this->uid;
   $this->table='';
   $this->dbid=$dbid;
   $this->signal=$signal;
  }
  public function Add( &$element ) {
   $this->element[]=$element;
  }
  public function Insert( $key, &$element ) {
   foreach ( $this->element as &$e ) {
    if ( get_class($e) == 'FormInsert'
      && matches($key,$e->element_replacement_key) )
    $e=$element;
   }
  }
  public function Encode($data) {
   $cipher=new Cipher(form_salt);
   return urlencode($cipher->vigencypher($data));
  }
  public function Decode($data) {
   $cipher=new Cipher(form_salt);
   return $cipher->vigdecypher(urldecode($data));
  }
  public function Prepare() {
   $jq='';
   $js='';
   $html='<div id="'.$this->rap.'_invis" style="display:none;"></div>';
   foreach ( $this->element as $e ) {
    $js.=$e->js;
    $jq.=$e->jq;
    $html.=$e->html.PHP_EOL;
   }
   $submit=NULL;
   $this->map=array();
   foreach ( $this->element as &$e ) {
    if ( is_null($submit) && get_class($e) == 'FormSubmit' ) $submit=&$e;
    else if ( get_class($e) == 'FormInsert' ) continue;
    else if ( !false_or_null($e->field) ) $this->map[]=$this->rap.'__'.$e->name.'|'.$e->field;
   }
   if ( !is_null($submit) ) {
    global $ajax_unique;
    $ajax_unique++;
    $serialized='"&ajax=1&u='.$ajax_unique.'"'.PHP_EOL;
    $serialized.=' +"&rap='.FormHelper::Encode($this->rap).'"'.PHP_EOL;
    if ( count($this->map) > 0 )    $serialized.=' +"&map='.FormHelper::Encode(json_encode($this->map)).'"'.PHP_EOL;
    if ( $this->dbid !== NULL )     $serialized.=' +"&dbid='.FormHelper::Encode($this->dbid).'"'.PHP_EOL;
    if ( strlen($this->table) > 0 ) $serialized.=' +"&table='.FormHelper::Encode($this->table).'"'.PHP_EOL;
    if ( $this->signal !== NULL )   $serialized.=' +"&signal='.FormHelper::Encode(json_encode($this->signal)).'"'.PHP_EOL;
    foreach ( $this->element as &$e ) if ( $e != $submit && get_class($e) !== 'FormInsert' ) {
     $serialized.=' +"&'.$this->rap.'__'.$e->name.'="+encodeURIComponent(btoa('.$e->Data().'))'.PHP_EOL;
    }
    $jq.='$("#'.$submit->name.'").click(function(){'.PHP_EOL
        .'var url="'.$this->url.'"; $.ajax({'.PHP_EOL
        .'type: "POST", url: url, dataType: "html", data: '.$serialized
        .', success: function (r) { $("#'.$this->rap.'_invis").html(r); }'.PHP_EOL
        .' });'.PHP_EOL
        .'});'.PHP_EOL;
   }
   $this->jq=$jq;
   $this->js=$js;
   $this->html='<!--form: '.$this->uid.'--><div id="'.$this->rap.'"><FORM id="'.$this->name.'" name="'.$this->rap.'">'.PHP_EOL
    .$html
    .'</FORM></div><!--form: '.$this->uid.'-->'.PHP_EOL;
  }
 };


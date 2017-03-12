<?php

/*********************************************************************************************
 *  __    __________________   ________________________________   __________  ________       *
 * /\ \  /\  __ \  ___\__  _\ /\  __ \  ___\__  _\  == \  __ \ "-.\ \  __ \ \/\ \__  _\ (tm) *
 * \ \ \_\_\ \/\ \___  \/\ \/ \ \  __ \___  \/\ \/\  __<\ \/\ \ \-.  \  __ \ \_\ \/\ \/      *
 *  \ \_____\_____\_____\ \_\  \ \_\ \_\_____\ \_\ \_\ \_\_____\_\\"\_\_\ \_\_____\ \_\      *
 *   \/_____/_____/_____/\/_/   \/_/\/_/_____/\/_/\/_/\/_/_____/_/ \/_/_/\/_/_____/\/_/      *
 *    --------------------------------------------------------------------------------       *
 *     Page Framework (c) 2007-2016 H. Elwood Gilliland III - MIT Licensed Open Source       *
 *********************************************************************************************/

// global $plog_level; $plog_level=1;

 include_once 'path.php'; //<- change siteroot here to move site
 include_once 'errors.php';
 include_once 'utility.php';
 include_once 'unique.php';
 include_once 'engines.php';
 include_once 'root.php';
 include_once 'Database.php';
 include_once 'ui.php';

 // Basic (minimal) bootstrapping.
 include_once SITE_ROOT.'/settings/config.php';
 include_once SITE_ROOT.'/settings/config.flags.php';
 include_once SITE_ROOT.'/settings/config.enums.php';
 include_once SITE_ROOT.'/settings/config.global.php';
 include_once SITE_ROOT.'/settings/config.databases.php';
 include_all(SITE_ROOT.'/model/');
 include_all(SITE_ROOT.'/global/');
 include_all(SITE_ROOT.'/shreds/');
 include_all(SITE_ROOT.'/ui/');
 include_once 'Auth.php';
 // We're done!

 global $_bound;
 $_bound=0;
 class Header extends Root {
  var $string, $replace, $http_response_code;
  public function construct( $string, $replace=true, $http_response_code=200 ) {
   $this->string=$string;
   $this->replace=$replace;
   $this->http_response_code=$http_response_code;
  }
  public function Execute() {
   plog( 'header sent :'.$string );
   header( $string, $replace, $http_response_code );
  }
 };

 // All javascript source documents must exist in the folder siteroot/js/
 class Javascript extends Root {
  var $js;
  // $js may be either a string or an array, where the array is an array of
  // stylesheet names found under the js/ folder, or if it is a string it is
  // either a file (javascript) or inline SCRIPT tag content
  public function __construct( $js ) {
   if ( is_array($js) ) $this->js=$js;
   else if ( is_string($js) ) $this->js=$js;
   else error( 'Javascript:construct(`'.$js.'`)','parameter was not a string or array');
  }
  public function Source() {
   if ( is_string($this->js) && strlen($this->js) == 0 ) return '';
   if ( is_array($this->js) ) {
    $out="";
    foreach ( $this->js as $jsfile )
     if ( isfile($this->js,'js/') === TRUE )
      $out.='<SCRIPT src="'.'js/'.$jsfile.'" type="text/javascript"></SCRIPT>';
   } else {
    if ( is_string($this->js) ) {
     $words=count(words($this->js));
     if ( $words == 1 && contains($this->js,"//cdn" ) )
      return '<SCRIPT src="'.$this->js.'" type="text/javascript"></SCRIPT>';
     else if ( $words == 1 && contains($this->js,"http") && !contains($this->js,"\n") )
      return '<SCRIPT src="'.$this->js.'" type="text/javascript"></SCRIPT>';
     else if ( isfile($this->js,'js/',FALSE) )
      return '<SCRIPT src="'.'js/'.$this->js.'" type="text/javascript"></SCRIPT>';
     else if ( ($words == 1
        && !contains($this->js,"\n")
        && !contains($this->js,'"')
        && !contains($this->js,'$')
        && !contains($this->js,';')) )
      return '<SCRIPT type="text/javascript">'.$this->js.'</SCRIPT>';
     else // Assume it is an inline script tag
      return '<SCRIPT type="text/javascript">'.$this->js.'</SCRIPT>';
    } else {
     error("Javascript:Source()","Style input was set to non-array, non-string which is not supported");
     return "";
    }
   }
  }
 };


 // All CSS stylesheets must exist in the folder siteroot/css/
 class Stylesheet extends Root {
  var $style;
  // $style may be either a string or an array, where the array is an array of
  // stylesheet names found under the css/ folder, or if it is a string it is
  // either a file (stylesheet) or inline STYLE tag content
  public function __construct( $style ) {
   if ( is_array($style) ) $this->style=$style;
   else if ( is_string($style) ) $this->style=$style;
   else error('Stylesheet:construct(`'.$style.'`)','parameter was not an array or string');
  }
  public function Get($stylesheet) {
   $words=count(words($stylesheet));
   if ( stripos(trim($stylesheet),"//cdn.") ===0 )
   return '<LINK href="'.$stylesheet.'" rel="stylesheet" type="text/css">';
   if ( isfile($stylesheet,'css/',FALSE) === TRUE )
   return '<LINK href="'.'css/'.$stylesheet.'" rel="stylesheet" type="text/css">';
   else if ( ($words == 1 && !contains($stylesheet,"\n") && !contains($stylesheet,';'))
        || stripos(trim($stylesheet),"http")===0 )
   return '<LINK href="'.$stylesheet.'" rel="stylesheet" type="text/css">';
   else // Assume it is an inline style tag
   return '<STYLE type="text/css">'.$stylesheet.'</STYLE>';
  }
  public function Source() {
   if ( is_array($this->style) ) {
    $out="";
    foreach ( $this->style as $stylesheet ) $out.=$this->Get($stylesheet);
   } else $out=$this->Get($this->style);
   return $out;
  }
 };

 class View extends Root {
  var $name,$file,$fragment;
  public function __construct( $file ) {
   $this->file = $file;
   $this->fragment = array();
   $this->name = "";
   Load();
  }

  public function Load() {
   if ( is_array($this->file) ) {
    foreach ( $this->file as $file )
     if ( isfile($file,'view/') )
      $this->fragment[]=file_get_contents('view/'.$file);
   } else if ( is_string($this->file) ) {
    if ( isfile($this->file,'view/',FALSE) ) $this->fragment[]=file_get_contents($this->file);
    else $this->fragment[]=$this->file;
   } else error('View(`'.$s.'`)','Invalid type not string or array provided as filename');
  }

  public function Recurse( $page ) {
   foreach ( $this->fragment as &$fragment ) {
    $result=eval($fragment);
    if ( $result === NULL ) $fragment='';
    else $fragment=$result;
   }
  }

  public function Source() {
   $out="";
   foreach ( $this->fragment as $fragment ) $out.=$fragment;
   return $out;
  }
 };

 class KV {
  var $values;
  public function __construct() {
   $this->values=array();
  }
  public function Load( $name, $file ) {
   if ( isfile($file) === TRUE )
   $this->values[$name]=file_get_contents($file);
   else $this->values[$name]=NULL;
  }
  public function Set( $name, $value ) {
   $this->values[$name]=$value;
  }
  public function Get( $name, $value ) {
   return isset( $this->values[$name] ) ? $this->values[$name] : '';
  }
  public function Execute($name) {
   return eval(Get($name));
  }
 };

 class KVStack {
  var $values,$count;
  public function __construct() {
   $this->values=array();
   $this->count=0;
  }
  public function Load( $file ) {
   if ( isfile($file) === TRUE ) return Push(file_get_contents($file));
  }
  public function Push( $data ) {
   $this->values[]=$data;
   $this->count=count($data);
   return $this->count-1;
  }
  public function Pop() {
   $out=array_pop($this->values);
   $this->count=count($this->values);
   return $out;
  }
  public function Shift() {
   $out=array_shift($this->values);
   $this->count=count($this->values);
   Renumber();
   return $out;
  }
  public function Prepend( $data ) {
   $this->count=array_unshift($this->values,$data);
   Renumber();
  }
  public function Renumber() {
   $replace=array();
   foreach ( $this->values as $value ) $replace[]=$value;
   $this->values=$replace;
  }
 };

 class Page {

  var $name,$title,$viewport,$eol_source,$eol_html,$doctype,$header,$head,$body,$view,$kv,$stack,$ui,$jq,$jq_loaded,$angular_loaded;
  var $ua,$ajax;

  // $doctype parameter provides a way to override default HTML5 style document.
  // If $doctype is NULL, FALSE or an empty string "" it will not display, this is
  // used for special header cases where a DOCTYPE is not required, such as file
  // download pages and AJAX responses.
  public function __construct( $name="", $doctype='<!DOCTYPE html>' ) {
   $this->name=$name;
   $this->title=sitename;
   $this->eol_source="\n";
   $this->eol_html='<BR>';
   $this->html_start='<HTML>';
   $this->html_head_start='<HEAD>';
   $this->html_body_start='<BODY>';
   $this->header=array();
   $this->head=array();
   $this->body=array();
   $this->doctype=$doctype;
   $this->kv=new KV();
   $this->stack=new KVStack();
   $this->ui=array();
   $this->viewport="device-width";
   $this->jq=array();
   $this->jq_loaded=FALSE;
   $this->angular_loaded=FALSE;
   $this->ajax=Page::isAJAXed();
  }

  static public function isAJAXed() {
   return ( isset($_POST['ajax']) || isset($_GET['ajax']) ) ? TRUE : FALSE;
  }

  public function Name($name) {
   $this->name=$name;
  }

  public function is_Named($name) {
   return matches($name,$this->name);
  }

  public function Header( $string, $replace=true, $http_response_code=200 ) {
   $this->header[]=$returned=(new Header( $string, $replace, $http_response_code ));
   return $returned;
  }

  public function CSS( $as, $body=FALSE ) {
   return $this->Style($as,$body);
  }
  public function Style( $as, $body=FALSE ) {
   if ( $body !== FALSE ) $this->body[]=($returned=new Stylesheet( $as ));
   else $this->head[]=($returned=new Stylesheet( $as ));
   return $returned;
  }

  public function Font( $family ) {
   return '<link href="http://fonts.googleapis.com/css?'.$family.'" rel="stylesheet" type="text/css">';
  }
  public function FontLawyer( ) {
  }

  public function Fragment( $id, $uri, $params=NULL ) {
   global $ajax_unique;
   $ajax_unique++;
   if ( is_null($params) ) $params=array( "ajax"=>$ajax_unique, "u"=>$ajax_unique );
   else { $params['ajax']=$ajax_unique; $params['u']=$ajax_unique; }
   $code=PHP_EOL.'$.ajax({
  context:document, type:"POST", dataType:"html",
      url:"'.$uri.'",
     data: "'.ajaxvars($params,'&').'"
  }).done(function(r) { $("#'.$id.'").html(r); });'.PHP_EOL;
   $this->HTML('<div id="'.$id.'_fragment_invis" style="display:none"></div>'.$this->eol_source);
   return $this->Javascript($code,TRUE);
  }

  public function JS( $as, $body=FALSE ) {
   return $this->Javascript($as,$body);
  }
  public function Javascript( $as, $body=FALSE ) {
   if ( $body !== FALSE ) $this->body[]=($returned=new Javascript( $as ));
   else $this->head[]=($returned=new Javascript( $as ));
   return $returned;
  }

  // Adds to the jquery doc ready, loads jquery if not loaded
  public function JQ( $s ) {
   $this->Jquery();
   $this->jq[]=$s;
  }

  public function View( $as, $recurse=TRUE ) {
   if ( is_string($as) ) {
    $view=new View($as);
    if ( $recurse === TRUE ) $view->Recurse($this);
    $this->view[]=$view;
    return $view;
   } else if ( is_array($as) ) {
    $result=array();
    foreach ( $as as $a ) {
     $result[]=$this->View($a,$recurse);
    }
    return $result;
   } else error('Page:View(`'.$as.'`)','parameter was not a string or array');
  }
  /*
  public function pHTML( $phtml ) {
   if ( is_array($phtml) ) { // Load and append
   } else {
    $file=file_get_contents('phtml/'.$phtml);
    // extract the PHP
    $file.='<?php ?>'.$phtml;
    $result=eval($phtml);
   }
  }
  */
  public function HTML( $html, $replacements=FALSE, $body=TRUE ) {
   if ( isfile($html,'html/',FALSE) ) $html=file_get_contents('html/'.$html);
   $out='';
   if ( false_or_null($replacements)===TRUE ) {
    if ( is_array($html) ) foreach( $html as $h ) $out.=$h;
    else $out=$html;
   } else {
    if ( is_array($replacements) ) {
     if ( is_array($html) ) {
      foreach($html as $h) {
       if ( isfile($h,'html/',FALSE) )
       $replaced=file_get_contents('html/'.$h);
       else $replaced=$h;
       foreach ( $replacements as $string=>$replace ) $replaced=str_replace($string,$replace,$replaced);
       $out.=$replaced;
      }
     } else {
      if ( isfile($html,'html/',FALSE) ) $replaced=file_get_contents($html);
      else $replaced=$html;
      foreach ( $replacements as $string=>$replace ) $replaced=str_replace($string,$replace,$replaced);
      $out=$replaced;
     }
    } else error('Page:HTML(`'.$html.'`,`'.$replacements.'`','replacements provided was not array');
   }
   if ( $body === TRUE ) $this->body[]=$out;
   else $this->head[]=$out;
   return $out;
  }

  public function Source( $ajax = FALSE ) {
   // Figure out the UI contribution
   $ui_head_js="";
   $ui_body_js="";
   $ui_css="";
   $ui_js_data="";
   foreach ( $this->ui as $ui ) {
    $ui->_Implement();
    if ( count($ui->js_data)  > 0 ) $ui_js_data.=$ui->_GetPreloaded();
    if ( strlen($ui->head_js) > 0 ) $ui_head_js.=$ui->head_js;
    if ( strlen($ui->body_js) > 0 ) $ui_body_js.=$ui->body_js;
    if ( strlen($ui->css) > 0 ) $ui_css.=$ui->css;
   }
   // Generate the page source
   if ( !$this->ajax ) {
    $out=$this->doctype . $this->eol_source;
    $out.=$this->html_start . $this->eol_source;
    $out.=$this->html_head_start . $this->eol_source;
    $out.='<meta name="viewport" content="width='.$this->viewport.'">'.$this->eol_source;
    $out.='<TITLE>'.$this->title.'</TITLE>' . $this->eol_source;
    foreach ( $this->head as $head ) {
     $out.=is_string($head) ? $head : $head->Source() . $this->eol_source;
    }
    if ( strlen($ui_css) > 0 ) {
     $out.='<STYLE type="text/css">' . $this->eol_source
         . $ui_css . $this->eol_source
         . '</STYLE>' . $this->eol_source;
    }
   }
   if ( strlen($ui_js_data) > 0 ) {
    $out.='<SCRIPT type="text/javascript">' . $this->eol_source
          . $ui_js_data . $this->eol_source
          .'</SCRIPT>' . $this->eol_source;
   }
   if ( strlen($ui_head_js) > 0 ) {
    $out.='<SCRIPT type="text/javascript">' . $this->eol_source
          . $ui_head_js . $this->eol_source
          .'</SCRIPT>' . $this->eol_source;
   }
   if ( count($this->jq) > 0 ) {
    $doc_ready=implode($this->eol_source,$this->jq);
    if ( !$this->ajax ) {
     $out.='<SCRIPT type="text/javascript">'.$this->eol_source
         .'jQuery(document).ready(function(){/////'.$this->eol_source.$this->eol_source
         .$doc_ready.$this->eol_source.'});/////'.$this->eol_source.'</SCRIPT>'.$this->eol_source;
    } else $out.='<SCRIPT type="text/javascript">'.$this->eol_source.$doc_ready.$this->eol_source.'</SCRIPT>';
   }
   if ( !$this->ajax ) {
    $out.='</HEAD>' . $this->eol_source;
    $out.=$this->html_body_start . $this->eol_source;
   }
   if ( strlen($ui_body_js) > 0 ) {
    $out.='<SCRIPT type="text/javascript">' . $this->eol_source
          . $ui_body_js . $this->eol_source
          .'</SCRIPT>' . $this->eol_source;
   }
   foreach ( $this->body as $body ) {
    $out.=is_string($body) ? $body : $body->Source() . $this->eol_source;
   }
   if ( !$this->ajax ) {
    $out.='</BODY>' . $this->eol_source;
    $out.='</HTML>';
   }
   return $out;
  }

  public function AJAX() {
   print $this->Source(TRUE);
  }

  public function Render( $send_headers=TRUE ) {
   if ( $send_headers === TRUE && !headers_sent() )
    foreach ( $this->header as $header ) $header->Execute();
   print $this->Source();
  }

  static public function Redirect( $uri=FALSE, $force_js=FALSE ) {
   if ( $force_js === FALSE && Page::isAJAXed() ) $force_js=true;
   if ( $uri === FALSE ) $uri = $_SERVER['HTTP_REFERER'];
   plog( "page->Redirect: ".$uri );
   if ( headers_sent() || $force_js !== FALSE ) { echo redirect($uri); die; }
   else { header("Location: $uri"); die; }
  }

  public function Bootstrap() {
   $this->JS( CDN_BOOTSTRAP_JS );
   $this->CSS( CDN_BOOTSTRAP_CSS );
   $this->CSS( CDN_BOOTSTRAP_THEME );
  }

  public function Jquery( $ui=TRUE ) {
   if ( $this->jq_loaded === FALSE ) {
    $this->JS( CDN_JQUERY_LATEST );
    if ( $ui === TRUE ) {
     $this->JS( CDN_JQUERY_UI );
//     $this->CSS( CDN_JQUERY_UI_CSS );
//     $this->JS( 'jquery-ui.min.js' );
     $this->CSS( 'css/jquery-ui.css' );
     $this->CSS( 'css/jquery-ui.theme.css' );
    }
    $this->jq_loaded=TRUE;
   }
  }
  
  public function Angular( $suppress_ngapp_in_html=FALSE ) {
   if ( $this->angular_loaded === FALSE ) {
    $this->JS( CDN_ANGULAR_LATEST );
    $this->angular_loaded=TRUE;
    if ( $suppress_ngapp_in_html !== FALSE ) {
     $this->html_start='<HTML ng-app>';
    }
   }
  }  

  public function isDesktop() {
   return $this->isMobile() ? FALSE : TRUE;
  }

  public function isMobile() {
   $this->ua = new uagent_info;
   return ($this->ua->isTierTablet
        || $this->ua->isTierIphone
        || $this->ua->isMobilePhone
        || $this->ui->isAndroidPhone
        || $this->ua->isTierGenericMobile) ? TRUE : FALSE;
  }

  public function Viewport( $width="device-width" ) {
   $this->viewport=$width;
  }

  public function Anchor( $name ) {
   $this->HTML( '<a name="'.$name.'"></a>' );
  }

  public function Add( $o, $replacements=FALSE ) {
   if ( is_object($o) ) {
    if ( get_class($o) == 'DataForm' ) {
     $o->form->Prepare();
     $this->JQ($o->form->jq,TRUE);
     $this->JS($o->form->js);
     $this->HTML($o->form->html,$replacements);
    }
   } else {
   }
  }

  public function Table( $tablehelper ) {
   $tablehelper->Render($table);
   $this->HTML($table);
  }

  /* Functions for binding a page element to a database value */

  public function ProtectedBindToggle( $table, $id, $field, $value, $return_html=FALSE ) {
   global $_bound;
   $fun_name='BindToggle_'.$_bound;
   $html=
     '<div class="protected-bind-toggle-wrapper">'
    .'<div id="'.$fun_name.'_glass'.'" class="protected-bind-toggle-glass fi-unlock"></div>'
    .'<div class="protected-bind-toggle">'
    .'<input type="checkbox" name="'.$fun_name.'" id="'.$fun_name.'"'.(intval($value)==1?' checked':'').'>'
    .'</div>';
   $this->JQ('
   $("#'.$fun_name.'").attr("disabled","disabled");
   $("#'.$fun_name.'_glass").on("click", function() {
    $("#'.$fun_name.'_glass").hide();
     $("#'.$fun_name.'").removeAttr("disabled");
   });
   $("#'.$fun_name.'").on("click", function() {
    var evalue=$("#'.$fun_name.'").get(0).checked;
    $.ajaxSetup({ cache: false });
    $.ajax({
     cache:false,
     type: "POST",
     dataType: "JSON",
     url:"ajax.bound",
     data: { V:evalue?1:0, T:"'.$table.'", F:"'.$field.'", I:'.$id.' }
    }).done(function (e) {
     $("#'.$fun_name.'_glass").show();
     $("#'.$fun_name.'").attr("disabled","disabled");
    });
   })');
   $_bound++;
   if ( $return_html === FALSE ) {
    $this->HTML($html);
    return $fun_name;
   }
   return $html;
  }

  public function BindToggle( $table, $id, $field, $value, $return_html=FALSE ) {
   global $_bound;
   $fun_name='BindToggle_'.$_bound;
   $html='<input type="checkbox" name="'.$fun_name.'" id="'.$fun_name.'"'.(intval($value)==1?' checked':'').'>';
   $this->JQ('$("#'.$fun_name.'").on("change click", function() {
    var evalue=$("#'.$fun_name.'").get(0).checked;
    $.ajaxSetup({ cache: false });
    $.ajax({
     cache:false,
     type: "POST",
     dataType: "JSON",
     url:"ajax.bound",
     data: { V:evalue?1:0, T:"'.$table.'", F:"'.$field.'", I:'.$id.' }
    }).done(function (e) {
    });
   })');
   $_bound++;
   if ( $return_html === FALSE ) {
    $this->HTML($html);
    return $fun_name;
   }
   return $html;
  }

  // Most input is in this form.
  public function BindString( $table, $id, $field, $value, $placeholder, $return_html=FALSE, $classes="texty", $chars=20, $max=-1 ) {
   global $_bound;
   $fun_name='BindString_'.$_bound;
   $html=(
    '<input type="text" value="'.$value.'" placeholder="'.$placeholder.'" name="'.$fun_name.'" id="'.$fun_name.'" class="'.$classes.'" size="'.$chars.'"'.
    (!is_integer($max) || $max > 0 ? ' maxlength="'.$max.'"' : '').
    '>'
   );
   $this->JQ('$("#'.$fun_name.'").on("change keypress paste input", function() {
    TryToSave_'.$fun_name.'();
   });');
   $this->JS('
    var SavingTimeout_'.$fun_name.'=null;
    function TryToSave_'.$fun_name.'() {
     if ( SavingTimeout_'.$fun_name.'!=null ) window.clearTimeout(SavingTimeout_'.$fun_name.');
     SavingTimeout_'.$fun_name.'=setTimeout(function(){
      var evalue=$("#'.$fun_name.'").get(0).value;
      $.ajaxSetup({ cache: false });
      $.ajax({
       cache:false,
       type: "POST",
       dataType: "JSON",
       url:"ajax.bound",
       data: { V:evalue, T:"'.$table.'", F:"'.$field.'", I:'.$id.' }
      });
      SavingTimeout_'.$fun_name.'=null;
     },100);
    }');
   $_bound++;
   if ( $return_html === FALSE ) {
    $this->HTML($html);
    return $fun_name;
   }
   return $html;
  }

  // Tag entry
  public function BindStringTags( $table, $id, $field, $value, $placeholder="Enter tags, use comma to set", $return_html=FALSE, $height="50px", $classes="tags", $chars=100, $max=-1, $onChange='' ) {
   global $_bound;
   $fun_name='BindStringTags_'.$_bound;
   $html=(
    '<input type="text" value="'.$value.'" placeholder="'.$placeholder.'" name="'.$fun_name.'" id="'.$fun_name.'" class="'.$classes.'" size="'.$chars.'"'.
    (!is_integer($max) || $max > 0 ? ' maxlength="'.$max.'"' : '').
    '>'
   );
   $this->JS("\n".'
    var '.$fun_name.'unloaded=true;
    var '.$fun_name.'evalue="";
    function '.$fun_name.'callback(elem,elem_tags) {
     if ( '.$fun_name.'unloaded ) return;
     var evalue=$("#'.$fun_name.'").get(0).value;
     console.log("evalue = "+evalue);
     '.$fun_name.'evalue="";
     $(".tag", elem_tags).each(function() { '.$fun_name.'evalue+=","+$(this).text().toString(); });
    '.$onChange.'
     $.ajaxSetup({ cache: false });
     $.ajax({
      cache:false,
      type: "POST",
      dataType: "JSON",
      url:"ajax.bound",
      data: { V:evalue, T:"'.$table.'", F:"'.$field.'", I:'.$id.' }
     }).done(function (e) {
     });
    }
   ');
   $this->JQ('
    $("#'.$fun_name.'").tagsInput({
     onChange: '.$fun_name.'callback,
     onRemoveTag: '.$fun_name.'callback,
     onAddTag: '.$fun_name.'callback,
     defaultText:"'.$placeholder.'",
     width: "auto",
     height: "'.$height.'",
     delimiter: ","
    });
    '.$fun_name.'unloaded=false;
   ');
   $_bound++;
   if ( $return_html === FALSE ) {
    $this->HTML($html);
    return $fun_name;
   }
   return $html;
  }

  // Read a paragraph of text and retain the line endings.
  public function BindText( $table, $id, $field, $value, $placeholder, $return_html=FALSE, $classes="textentry wide", $rows=-1, $cols=-1 ) {
   global $_bound;
   $fun_name='BindText_'.$_bound;
   $html=(
    '<textarea '.(isset($this->loaded_bound_plugins) && $this->loaded_bound_plugins === TRUE ? 'data-widearea="enable" ' : '').'placeholder="'.$placeholder.'" name="'.$fun_name.'" id="'.$fun_name.'" class="'.$classes.'"'.
    ( !is_integer($rows) || $rows > 0 ? ' rows="'.$rows.'"' : '' ).
    ( !is_integer($cols) || $cols > 0 ? ' cols="'.$cols.'"' : '' ).
    '>'.$value.'</textarea>'
   );
   $this->JS('var SavingTimeoutBindText_'.$fun_name.'=null;');
   $this->JQ('$("#'.$fun_name.'").on("change keypress paste input click focusin focusout focus", function() {
    if ( SavingTimeoutBindText_'.$fun_name.'!=null ) window.clearTimeout(SavingTimeout_'.$fun_name.');
    SavingTimeoutBindText_'.$fun_name.'=setTimeout( function() {
     var evalue=$("#'.$fun_name.'").get(0).value;
     $.ajaxSetup({ cache: false });
     $.ajax({
      cache:false,
      type: "POST",
      dataType: "JSON",
      url:"ajax.bound",
      data: { V:evalue, T:"'.$table.'", F:"'.$field.'", I:'.$id.' }
     }).done(function (e) {
      SavingTimeoutBindText_'.$fun_name.'=null;
     });
    },100);
   });');
   $_bound++;
   if ( $return_html === FALSE ) {
    $this->HTML($html);
    return $fun_name;
   }
   return $html;
  }

  public function BindSelect( $table, $id, $field, $selected, $placeholder, $return_html=FALSE ) {
  }

  public function BindNumberSelect( $id, $value ) {
  }

  var $loaded_bound_plugins;
  public function Bind_LoadPlugins() {
   if ( !isset($this->loaded_bound_plugins) || $this->loaded_bound_plugins !== TRUE ) {
    $this->loaded_bound_plugins=TRUE;
    $this->JQ_Chosen();
    $this->JQ_ImagePicker();
    $this->JS_Widearea();
    $this->JQ_TagsInput();
    $this->JQ_Tipster();
    $this->JQ_Spectrum();
   }
  }

  // Macros for commonly loaded plugins

  public function JQ_Chosen() {  // A smart searchable combo dropdown
   $this->JS('chosen.jquery.min.js');
   $this->CSS('chosen.css');
  }
  public function JQ_ImagePicker() { // A nice way to show an icon grid that allows selections
   $this->JS('image-picker.js');
   $this->CSS('image-picker.css');
  }
  public function JS_Widearea() { // An advanced text entry medium
   $this->JS('widearea.min.js');
   $this->CSS('widearea.min.css');
   $this->JQ('wideArea();');
  }

  public function JQ_TagsInput() { // A way to enter tags and have them look cool
   $this->JS('jquery.tagsinput.js');
   $this->CSS('jquery.tagsinput.css');
  }

  public function JQ_Tipster() {
   $this->CSS('tooltipster.css');
   $this->JS('jquery.tooltipster.min.js');
  }

  public function JQ_Spectrum() {
   $this->CSS('spectrum.css');
   $this->JS('spectrum.js');
  }


  public function Tips( $icon, $title, $arr_content, $return_html=FALSE ) {
   $content='<div><b>'.$title.'</b></div>';
   foreach ($arr_content as $named=>$crisis ) {
    if ( is_integer($named) ) {
     $content.="<p>".$crisis."</p>";
    } else $content.='<p><b>'.$named.'</b>: '.$crisis.'</p>';
   }
   global $_tipsters;
   $_tipsters++;
   $id='tipster'.$_tipsters;
   $this->JS('var '.$id.'_stay=false;');
   $this->JQ('
    $("#'.$id.'").tooltipster({animation:"fade",maxWidth:"90%",content:$(\''.$content.'\')});
    $("#'.$id.'").click(function(e){
     if ( '.$id.'_stay ) {
      $("#'.$id.'").tooltipster("option","autoClose","true");
      '.$id.'_stay=false;
     } else {
      $("#'.$id.'").tooltipster("option","autoClose","false");
      '.$id.'_stay=true;
     }
    });
   ');
   if ( $return_html ) return '<span id="'.$id.'" class="tooltipstericon">'.$icon.'</span>';
   else $this->HTML('<span id="'.$id.'" class="tooltipstericon">'.$icon.'</span>');
  }

  public function BindColorRGB( $id, $table, $field, $value, $allowempty=FALSE ) {
   global $_color_rgbs;
   $_color_rgbs++;
   $dom='crgb_'.$_color_rgbs;
   $this->HTML('<input id="'.$dom.'" type="text" value="'.$value.'"/>');
   $this->JQ('
    $("#'.$dom.'").spectrum({
          theme:"sp-dark",
     cancelText:"",
     chooseText:"Done",
    preferredFormat: "rgb",
      showInput:true,
    showPalette:true,
    palette: [
        ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
        ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
        ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
        ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
        ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
        ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
        ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
        ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
    ],
    togglePaletteMoreText: "&raquo;",
    togglePaletteLessText: "&laquo;",
     allowEmpty:'.($allowempty === TRUE ? 'true' : 'false').'
    }).on("change move.spectrum change.spectrum click",function(e,color){
//     console.log(color);
     var evalue=color.toHexString();
//     console.log(evalue);
     $.ajaxSetup({ cache: false });
     $.ajax({
      cache:false,
      type: "POST",
      dataType: "JSON",
      url:"ajax.bound",
      data: { V:evalue, T:"'.$table.'", F:"'.$field.'", I:'.$id.' }
     }).done(function(e){ });
    });
   ');
  }

  public function BindColorRGBA( $id, $table, $field, $value ) {
   global $_color_rgbs;
   $_color_rgbs++;
   $dom='crgb_'.$_color_rgbs;
   $this->HTML('<input id="'.$dom.'" type="text" value="'.$value.'"/>');
   $this->JQ('
    $("#'.$dom.'").spectrum({
     showAlpha:true,
     theme:"sp-dark",
     cancelText:"",
     chooseText:"Done",
      showInput:true,
    preferredFormat: "rgb",
    showPalette:true,
    palette: [
        ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
        ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
        ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
        ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
        ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
        ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
        ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
        ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
    ],
    togglePaletteMoreText: "&raquo;",
    togglePaletteLessText: "&laquo;",
     allowEmpty:false
    }).on("change move.spectrum change.spectrum click",function(e,color){
//     console.log(color);
     var evalue=color.toRgbString();
//     console.log(evalue);
     $.ajaxSetup({ cache: false });
     $.ajax({
      cache:false,
      type: "POST",
      dataType: "JSON",
      url:"ajax.bound",
      data: { V:evalue, T:"'.$table.'", F:"'.$field.'", I:'.$id.' }
     }).done(function(e){ });
    });
   ');
  }

  public function Note( $html ) {
   $this->HTML('<span class="notice">'.$html.'</span>');
  }

 };

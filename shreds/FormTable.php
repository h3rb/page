<?php

 /**
  * FormTable
  * Draws a form that is a multi-input spreadsheet with selectable rows.
  * Expects an array of key/value pairs that describe the form row scaffold,
  * and any ancillary settings that turn on and off various features.
  * Any omitted value has an obvious default.
  * "data" arrays provided can contain a keyword "disabled"=>false which indicates the row's disabled status
  *
  * Settings:
  *  "add"    When not (false), the content of the button.
  *  "button" When (false), draws the Add and Delete buttons as links.
  *  "delete" When not (false), specifies the content of the delete button and enables row deletion.
  *  "mode"   Set to "multiselect" to activate multiple selection features.
  *  "data"   Expects a list of numerically indexed results from a database query.
  *  "header" An array (required) that specifies column names, or "" for no header, corresponding to 'rows'
  *  "rows"   An array of arrays which is ordered and follows the "row object interface" specified below.
  *  "changed" Callback called when input is changed, passed content of table.
  *
  * CSS-related settings:
  * ---> Allow specific styling options for otherwise auto-generated classes
  *  "css"    By default "form", which is the prefix to css form classes, ie form_input form_phone
  *  "th"     Overrides the default CSS class "heading" for <th> elements in table headers.
  *  "tr"     Overrides the default CSS class "row" for <tr> elements in the table body.
  *  "select" Overrides the default CSS class "selected" for rows when they are selected.
  *  "highlight"  Overrides the default CSS class "highlight" for rows when they are highlighted.
  *  "disable" Overrides the default CSS class "disabled" for rows that are marked disabled.
  *
  * Javascript 'callback' bindings:
  * ---> Special settings that take the form of Javascript:function(this); and use rowin_id() function
  * ---> May not properly
  *  "disabled"
  *  "selected"
  *  "deselected"
  *  "hover"
  *  "leave"
  *
  * Javascript function hooks:
  * Post-pended with the unique ID of the "formtable"...
  *  togsel_id(row)        toggles the row of the table
  *  formtable_id()        returns the content in a .ajax( data: form as a uri param list
  *  high_id(row)          turns highlighting on a row
  *  unhigh_id(row)        turns highlight off on a row
  *  rowin_id(ele)         finds the row an element is in (for callbacks)
  * -- all other functions are internal -- use with caution
  *
  * Row Object Interface:
  * A set of key/value pairs in an ordered array of arrays specifying the columns and their form elements.
  * Each element of the "rows" setting array can take advantage of some or all of these features:
  *
  * "i"=>"..."  (input tag type) where ... is one of the following:
  *     "phone", "date", "datetime", "text", "textarea", "checkbox", "hidden", "zip", "select" (requires "v"=>array("named"=>value))
  *             note: you could add other ones like "slider", "numeric", etc.
  *             note: "phone" type requires jquery.ui.keypad
  *             note: "date" and "datetime" require jquery.ui.datepicker
  * "f"=>"fieldname"  where fieldname corresponds to a "data" field
  * "a"=>"..."  (align) where ... is "left", "right" or "center"
  * "m"=>"..."  (mask) where ... defines "masked input" of jQuery plugin "maskedinput" ie: (999)999-9999 ?x99999 where ? means "optional"
  *             note: masked input is currently only available for user-inserted rows due to a bug in the plugin
  * "r"=>true   (readonly) Add this to a row definition only when the element should be readonly or disabled
  *             note: if this is set to (true) and "i" is not provided, content is wrapped in a span with class "data"
  * "l"=>"..."  (label) Sets the label corresponding to this column in the row
  * "c"=>"..."  (caption) Sets the "caption" (uses CSS class "caption") content ie: "quantity" or "first, last"
  * "e"=>"..."  (default Element value) When specified, provides a default value for elements with no data associated with them or in a newly inserted row(?)
  * "w"=>#      (width) Specifies element width for text and textarea types
  * "h"=>#      (height) Specifies element height (rows) for textarea tags
  * "j"=>"..."  (javascript) Specifies a callback function in the form of "javascript:function(this);" called on an "appropriate" onblur, onchange
  */
 // mode: multiselect or "default" (select or clickthrough depending on url/ajax setting)
 // button: false=link or true=button

class FormTable extends Unique {

 var $rowForm,$settings,$id;
 public function __construct( $s ) {
  $this->Uniqueness();
  $this->settings=array();
  $this->Set($s);
 }

 function Set( $s ) {
  if ( !isset($s['changed']) )  $s["changed"]=''; // Makes the onchange event have no effect.
  if ( !isset($s['add'])    )    $s['add']=false;
  if ( !isset($s['debug'])  )    $s['debug']=false;
  if ( !isset($s['mode'])   )    $s['mode']='default';
  if ( !isset($s['delete']) )    $s['delete']=false;
  if ( !isset($s['button']) )    $s['button']=true;
  if ( !isset($s['url'])    )    $s['url']=false;
  if ( !isset($s['ajax'])   )    $s['ajax']=false;
  if ( !isset($s['header']) )    echo 'FormTable: Header was not defined, so cannot be mapped to data.';
  if ( !isset($s['row'])    )    echo 'FormTable: Row form was not defined, so cannot render.';
  if ( !isset($s['css'])    )    $s['css']="form";
  if ( !isset($s['th'])     )    $s['th']="heading";
//  if ( !isset($s['td'])     )    $s['td']="cell";
  if ( !isset($s['tr'])     )    $s['tr']="row";
  if ( !isset($s['select']) )    $s['select']='selected';
  if ( !isset($s['highlight']) ) $s['highlight']='highlight';
  if ( !isset($s['disable']) )   $s['disable']='disabled';
  if ( !isset($s['heading']))    $s['heading']='heading';
  if ( !isset($s['data'])   )    $s['data']=array();
  if ( !isset($s['disabled']))   $s['disabled']  ='none_'.$this->id;
  if ( !isset($s['selected']))   $s['selected']  ='none_'.$this->id;
  if ( !isset($s['deselected'])) $s['deselected']='none_'.$this->id;
  if ( !isset($s['hover']))      $s['hover']  ='none_'.$this->id;
  if ( !isset($s['leave']))      $s['leave']  ='none_'.$this->id;
  $this->settings=$s;
 }

 function Row( &$p, $u, $n, $in, $js, &$rowjs ) {
  $rowjs='';
  $tags="";
  $fc=$this->settings['css'];
  $f_counter=0;
  foreach ( $this->settings['row'] as $form ) {
     if ( isset($i) ) unset($i);
     if ( isset($f) ) unset($f);
     $l='';
     $e='';
     if ( isset($d) ) unset($d);
     if ( isset($j) ) unset($j);
     if ( isset($r) ) unset($r);  if ( isset($in['disabled']) && $in['disabled'] === true ) $r=true;
     if ( isset($w) ) unset($w);
     if ( isset($h) ) unset($h);
     if ( isset($c) ) unset($c);
     if ( isset($m) ) unset($m);
     if ( isset($v) ) unset($v);
     $a='left';
     foreach ( $form as $name=>$value ) { 
      $n___=strtolower(substr($name,0,1));
      switch ( $n___[0] ) {
       case 'm': // mask to use
         $m=$value;
        break;
       case 'a': // alignment (left,right,center) not ACL required to render this part of the form (an array used for the ACL call)
         $a=$value;
        break;
       case 'e': // Default value when no data to get
         $e=$value;
        break;
       case 'l': // label
         $l=$value;
        break;
       case 'j': // javascript
         $j=$value;
        break;
       case 'i': // input type of text, textarea, phone, select, date, checkbox, datatable
         $i=$value;
        break;
       case 'f': // field name
         $f=$value;
        break;
       case 'r': // readonly?
         if ( !isset($r) ) $r=$value;
        break;
       case 'w': // for determining element width
         $w=$value;
        break;
       case 'h': // for determining element height
         $h=$value;
        break;   // for captions
       case 'c':
         $c=$value;
        break;
       case 'v':
         $v=$value;
        break;
      }
     }
     $id=$f;
     if ( !isset($i) || (!isset($i) && (isset($r) && $r === true) )) {
      $tag='<span class="data">'.(isset($in[$f_counter])?$in[$f_counter]:'').'</span>';
      $tags.='<td align="'.$a.'">'.(isset($l)?'<span class="'.$fc.'_label">'.$l."</span>":'')
              .$tag.(isset($c)?'<span class="'.$fc.'_caption'.'">'.$c.'</span>':'').'</td>';
     }
     else
     if ( isset($i) ) {
      if ( isset($r) && $r === true ) $r=($i=="checkbox"||$i=="button"?'disabled ':'readonly '); else $r='';
      if ( $i == "hidden" ) {
       $tag='<input '.$js.' onchange="ft_onchange_'.$u.'();" id="'.$f.'_'.$u.'_'.$n.'" type="hidden" name="'.$l.'" '.$r.'value="'.(isset($in[$f_counter])?$in[$f_counter]:$e).'">';
      } else
      if ( $i == "text" ) {
       $tag='<input '.$js.' onchange="ft_onchange_'.$u.'();" class="'.$fc.'_'.$i.'" type="text" width="'.(isset($w)?$w:40).'" '.$r.'value="'.(isset($in[$f_counter])?$in[$f_counter]:$e).'" '
           .(isset($j)?'onblur="'.$j.'" ':'')
           .'id="'.$f.'_'.$u.'_'.$n.'">';
      } else
      if ( $i == "textarea" ) {
       $tag='<textarea '.$js.' onchange="ft_onchange_'.$u.'();" class="'.$fc.'_'.$i.'" id="'.$f.'_'.$u.'_'.$n.'" '.$r
           .(isset($j)?'onblur="'.$j.'" ':'')
           .'cols="'.(isset($w)?$w:20).'" rows="'.(isset($h)?$h:20).'">'.(isset($in[$f_counter])?$in[$f_counter]:'').'</textarea>';
      } else
      if ( $i == "date" ) {
       $tag='<input '.$js.' onchange="ft_onchange_'.$u.'();" class="'.$fc.'_'.$i.'" type="text" '.$r.'value="'.(isset($in[$f_counter])?date('m/d/Y',strtotime((isset($in[$f_counter])?$in[$f_counter]:$e))):'').'" width="'.(isset($w)?$w:20).'" onBlur="javascript:validDate(this);" id="'.$f.'_'.$u.'_'.$n.'">';
       if ( $n != "###" && !(isset($in['disabled']) && $in['disabled']===true)) $p->JQ('$(function(){$("#'.$f.'_'.$u.'_'.$n.'").datepicker({changeMonth:true,changeYear:true});});');
       else $rowjs.='$("#'.$f.'_'.$u.'_'.$n.'").datepicker({changeMonth:true,changeYear:true});';
      } else
      if ( $i == "datetime" ) {
       $tag='<input '.$js.' onchange="ft_onchange_'.$u.'();" class="'.$fc.'_'.$i.'" type="text" '.$r.'value="'.(isset($d)?date('r',strtotime((isset($in[$f_counter])?$in[$f_counter]:$e))):'').'" width="'.(isset($w)?$w:20).'" onBlur="javascript:validDate(this);" id="'.$f.'_'.$u.'_'.$n.'">';
       if ( $n != "###" ) $p->JQ('$(function(){$("#'.$f.'_'.$u.'_'.$n.'").datepicker({changeMonth:true,changeYear:true});});');
       else $rowjs.='$("#'.$f.'_'.$u.'_'.$n.'").datepicker({changeMonth:true,changeYear:true});';
      } else
      if ( $i == "zip" ) {
       $tag='<input '.$js.' onchange="ft_onchange_'.$u.'();" class="'.$fc.'_'.$i.'" '
           .(isset($j)?'onblur="'.$j.'" ':'')
           .'type="text" value="'.(isset($in[$f_counter])?$in[$f_counter]:$e).'" '.$r.'id="'.$f.'_'.$u.'_'.$n.'" width="'.(isset($w)?$w:5).'">';
      } else
      if ( $i == "phone" ) {
       if ( !isset($m) && $n=="###" ) $m="(999)999-9999 ?x99999";
       $tag='<input '.$js.' onchange="ft_onchange_'.$u.'();" id="'.$f.'_'.$u.'_'.$n.'" class="'.$fc.'_'.$i.'" type="text" value="'.(isset($in[$f_counter])?$in[$f_counter]:$e).'" width="'.(isset($w)?$w:15).'" maxlength="15">';
       if ( $n != "###" ) $p->JQ('$(function(){$("#'.$f.'_'.$u.'_'.$n.'").keypad({showOn:"focus",keypadOnly:false});});');
       else $rowjs.='$("#'.$f.'_'.$u.'_'.$n.'").keypad({showOn:"focus",keypadOnly:false});';
      } else
      if ( $i == "checkbox" ) {
       if ( !isset($e) || strlen($e)==0 ) $e=0;
       $tag='<input '.$js.' onchange="ft_onchange_'.$u.'();" class="'.$fc.'_'.$i.'" type="checkbox" '.$r.'value="'.(isset($in[$f_counter])?$in[$f_counter]:$e).'" '
           .(isset($j)?'oncheckbox="'.$j.'" ':'')
           .'id="'.$f.'_'.$u.'_'.$n.'"'.(intval((isset($in[$f_counter])?$in[$f_counter]:$e))==1?' checked':'').'>';
      } else
      if ( $i == "select" ) {
       $tag='<select '.$js.' onchange="ft_onchange_'.$u.'();" id="'.$f.'_'.$u.'_'.$n.'" name="'.$l.'">';
       foreach ( $v as $named=>$value ) {
        $tag.='<option value="'.$value.'"'.($in[$f_counter]==$value?" selected":'').'>'.$named;
       $tag.='</select>';
      }
      $tags.='<td align="'.$a.'">'.($i!="hidden"&&isset($l)?'<span class="'.$fc.'_label">'.$l."</span>":'')
              .$tag.(isset($c)?'<span class="'.$fc.'_caption'.'">'.$c.'</span>':'').'</td>';
      if ( $n=="###" ) {
       if ( isset($m) && $m!== false ) $rowjs.='$("#'.$f.'_'.$u.'_'.$n.'").mask("'.$m.'",{placeholder:" "});';
      }
     }
   $f_counter++;
  }
  return $tags
           .($this->settings['delete']!==false ?
             ( $this->settings['button'] === true
               ?'<td><input '.($in['disabled']?'disabled="disabled" ':'').'class="'.$fc.'_button" type="button" onclick="javascript:delrow_'.$this->id.'(this);" value="'.$this->settings['delete'].'"></td>'
               :'<td>'.($in['disabled']!==false?'':'<a href="javascript:delrow_'.$this->id.'(this);">')
                      .$this->settings['delete']
                      .($in['disabled']!==false?'':'</a>')
               .'</td>'
             ) :'');
 }

 public function Render( &$p ) {
  $u=$this->id;
  $jq="";

  $hoverjs=$this->settings['hover'];
  $leavejs=$this->settings['leave'];
  $disabledjs=$this->settings['disabled'];
  $selectedjs=$this->settings['selected'];
  $deselectedjs=$this->settings['deselected'];
  $heading=$this->settings['heading'];
  $th=$this->settings['th'];
  //$td=$this->settings['td'];
  $tr=$this->settings['tr'];
  $scss = $this->settings['select'];
  $dcss = $this->settings['disable'];
  $hcss = $this->settings['highlight'];
  $data=$this->settings['data'];

  $incoming=count($data);
  $rowjs='';

  $header='';
  $headers=count($header);
  foreach ( $this->settings['header'] as $name ) $header.='<th class="'.$th.'">'.$name.'</th>';

  $ids='';
  $falses='';
  $t=count($this->settings['data']);
  $n=1;
  foreach ( $this->settings['data'] as $r ) {
   $t--;
   $ids.="'".'ft_'.$u.'_'.$n."'".($t>0?',':'');
   $falses.="false".($t>0?',':'');
   $n++;
  }

  $fields='';
  $t=count($this->settings['row']);
  $n=1;
  foreach ( $this->settings['row'] as $r ) {
   $t--;
   if ( isset($r['f']) ) $f=$r['f']; else $f='';
   $fields.="'".$f."'".($t>0?',':'');
   $n++;
  }

  $j='onclick="javascript:togsel_'.$u.'(rowin_'.$u.'(this));"';

  $disabled='';
  $startrows="";
  $i=0;
  $t=count($data);
  foreach ( $data as $row ) { $i++; $t--;
   $disabled.=(isset($row['disabled'])&&$row['disabled']===true?'true':'false').($t>0?',':'');
   if ( isset($row['disabled']) && $row['disabled'] === true ) {
   $startrows.='<tr class="'.$dcss.'" id="ft_'.$u.'_'.$i.'" onclick="javascript:togsel_'.$u.'(this);">'
  .$this->Row($p,$u,$i,$row,$j,$rowjs).'</tr>';
   } else {
  $startrows.='<tr class="'.$tr.'" id="ft_'.$u.'_'.$i.'" onmouseover="javascript:high_'.$u.'(this);" onmouseout="javascript:unhigh_'.$u.'(this);" onclick="javascript:togsel_'.$u.'(this);">'
  .$this->Row($p,$u,$i,$row,$j,$rowjs).'</tr>';
   }
  }

  $formrow=$this->Row($p,$u,"###",array(),$j,$rowjs);

  $p->JS( 
   ( $this->settings['debug'] !== false
     ? "
     function debug_$u() {
      var t = document.getElementById('ft_$u');
      var out='ft_$u: #rows='+rows_$u+'\\n';
      for ( var i=1; i<rows_$u+1; i++ ) {
       out+=i+') rowid: '+rowids_".$u."[i-1]+' selected: '+rowsel_".$u."[i-1]+' disabled:'+row_disabled_".$u."[i-1]+'\\n';
      }
      alert(out+'content:\\n'+formtable_$u());
     }
     "
     : '' )
  .
  "
   var row_disabled_$u=new Array($disabled);
   var row_fields_$u=new Array($fields);
   var rowids_$u=new Array($ids);
   var rowsel_$u=new Array($falses);
   var rows_$u=$incoming;
   var ignoreclick_$u=0;
   function sel_all_$u(o) {
    var i=1;
    for ( ; i<rows_$u+1; i++ ) if ( !row_disabled_".$u."[i-1] ) {
     rowsel_".$u."[i-1]=!true;
     $('#'+rowids_".$u."[i-1]).addClass('selected');
     $('#'+rowids_".$u."[i-1]).removeClass('$tr');
    } else continue;
   }
   function unsel_all_$u(o) {
    var i=1;
    for ( ; i<rows_$u+1; i++ ) if ( !row_disabled_".$u."[i-1] ) {
     rowsel_".$u."[i-1]=false;
     $('#'+rowids_".$u."[i-1]).removeClass('selected');
     $('#'+rowids_".$u."[i-1]).addClass('$tr');
    } else continue;
   }
   function none_$u(o) {}
   function rowin_$u(o) { var node = o; while (node && node.tagName != 'TR') { node = node.parentNode } return node; }
   function togsel_$u(o) {
    if ( ignoreclick_$u>0) { ignoreclick_$u--; return; }
    for ( var i=1; i<rows_$u+1; i++ ) if ( rowids_".$u."[i-1] == o.id ) break;
   ".
   ( $this->settings['mode'] == 'multiselect'
    ? "if ( rowsel_".$u."[i-1] && !row_disabled_".$u."[i-1] ) {
        $('#'+o.id).removeClass('".$scss."');
        $('#'+o.id).addClass('".$tr."');
        if ( rowsel_".$u."[i-1] ) { ".$deselectedjs."(o); }
        rowsel_".$u."[i-1]=false;
       } else if ( !row_disabled_".$u."[i-1] ) {
        $('#'+o.id).addClass('".$scss."');
        $('#'+o.id).removeClass('".$tr."');
        if ( !rowsel_".$u."[i-1] ) { ".$selectedjs."(o); }
        rowsel_".$u."[i-1]=true;
       } else ".$disabledjs."(o);
      "
    : ( $this->settings['url'] === false
      ? "if ( row_disabled_".$u."[i-1] ) {
         ".$disabledjs."(o);
         return;
        }
        if ( rowsel_".$u."[i-1] ) {
          $('#'+o.id).removeClass('".$scss."');
          $('#'+o.id).addClass('".$tr."');
          ".$deselectedjs."(o);
          rowsel_".$u."[i-1]=false;
         }
       else {
          for ( var j=0; j<rowids_$u.length; j++ ) if ( rowsel_".$u."[j] ) {
          var p=document.getElementById(rowids_".$u."[j]);
          ".$deselectedjs."(p);
           $('#'+p.id).removeClass('".$scss."');
           $('#'+p.id).addClass('".$tr."');
           rowsel_".$u."[j]=false;
          }
          $('#'+o.id).removeClass('".$tr."');
          $('#'+o.id).addClass('".$scss."');
          ".$selectedjs."(o);
          rowsel_".$u."[i-1]=true;
      }
      "
      : ( $this->settings['ajax'] === false
          ? "if ( !row_disabled_".$u."[i-1] ) window.location='".$this->settings['url']."&selected='+(i-1);"
          : " $.ajax({ url:'".$this->settings['url']."&selected='+(i-1), dataType: 'html',
              method: 'post', context: document.body, success: function (data) { $('#".$this->settings['ajax']."').html(data); } });"
        )
      )
   )."
   }
   function ft_onchange_$u() {
    ".$this->settings["changed"]."(ft_get_values_$u());
   }
   function ft_get_values_$u() {
    var x=row_fields_$u.length;
    var y=rowids_$u.length;
    var ok= Array.apply(null, Array(y)).map(e => Array(x));
    for ( var j=0; j<y; j++ ) {
     for ( var i=0; i<x; i++ ) {
      ok[j][i]=$('#'+row_fields_".$u."[i]+'_".$u."_'+(j+1)).get(0).value;
     }
    }
    return ok;
   }
   function rem_$u(n,a) {
    var na=Array(); var i,c=0;
    for ( i=0; i<n; i++ ) { na[c]=a[i]; c++; } i++;
    for ( ; i<a.length; i++ ) { na[c]=a[i]; c++; }
    return na;
   }
   function delrow_$u(o) {
    var t = document.getElementById('ft_$u');
    var rows=Array();
    var r=rowin_$u(o);
    var i;
    for (i=0; i < rowids_".$u.".length; i++ ) if ( rowids_".$u."[i] == r.id ) break;
    var rn=i;
    rowsel_".$u."=rem_".$u."(i,rowsel_".$u."); //.splice(1,1);
    rowids_".$u."=rem_".$u."(i,rowids_".$u."); //.splice(1,1);
    row_disabled_".$u."=rem_".$u."(i,row_disabled_".$u."); //.splice(1,1);
    t.deleteRow(rn+1);
    rows_".$u."--;
    for ( i=0; i< rows_".$u."; i++ ) rows[i]=document.getElementById(rowids_".$u."[i]);
    for ( i=0; i< rows_".$u."; i++ ) {
     var cols=Array();
     for ( j=0; j< row_fields_".$u.".length; j++ ) {
      var cell=document.getElementById(repAll_$u(rows[i].id,'ft',row_fields_".$u."[j]));
      cell.id=row_fields_".$u."[j]+'_".$u."_'+(i+1);
     }
     rows[i].id=rowids_".$u."[i]='ft_".$u."_'+(i+1);
    }
    ignoreclick_$u=1;
   }
   function high_$u(o)   {
    for (i=0; i < rowids_".$u.".length; i++ ) if ( rowids_".$u."[i] == o.id ) break;
    $('#'+o.id).addClass('".$hcss."');
    if ( !rowsel_".$u."[i] ) $('#'+o.id).removeClass('$tr');
    ".$hoverjs."(o);
   }
   function unhigh_$u(o) {
    $('#'+o.id).removeClass('".$hcss."');
    for ( i=0; i < rowids_".$u.".length; i++ ) if ( rowids_".$u."[i] == o.id ) break;
    if ( !rowsel_".$u."[i] ) $('#'+o.id).addClass('$tr');
    ".$leavejs."(o);
   }
   function repAll_$u(x,y,z) {
    var s=x; var i=s.indexOf(y);
    while (i != -1){ s=s.replace(y,z); i= s.indexOf(y); } return s;
   }
   function addRow_$u() {
    var t = document.getElementById('ft_$u');
    rows_$u++;
    var r = t.insertRow(rows_$u);
    r.id='ft_".$u."_'+rows_$u;
    r.onmouseover=new Function('high_$u(this);');
    r.onmouseout=new Function('unhigh_$u(this);');
    r.onclick=new Function('togsel_$u(this);');
    var h='$formrow';
    var js='$rowjs';
    h=repAll_$u(h,'###',''+rows_$u);
    js=repAll_$u(js,'###',''+rows_$u);
    setTimeout(js,1);
    r.innerHTML=h;
    r.class='$tr';
    rowids_$u.push(r.id);
    rowsel_$u.push(false);
    row_disabled_$u.push(false);
   }

   function formtable_$u() {
    var out='';
    var s='';
    for ( var i=1; i<rows_$u+1; i++ ) {
     j=i-1;
     for ( var k=0; k<row_fields_$u.length; k++ ) {
      var id=row_fields_".$u."[k]+'_".$u."_'+i;
      var v=document.getElementById(id).value;
      out+='&ft_'+row_fields_".$u."[k]+'_$u'+'_'+i+'='+escape(v);
     }
     if ( rowsel_".$u."[j] ) s+=''+i+',';
    }
    if ( s!='' ) out+='&selected_$u='+escape(s);
    return out;
   }

  ". ( $this->settings['delete'] !== false
       ? ""
       : ''
     )
  );
  $p->HTML('
  <table cellpadding=0 cellspacing=0 width="100%" id="ft_'.$u.'">
   <tr class="$heading">'.$header.'</tr>
'.$startrows.'
  </table>
  '.( $this->settings['add'] !== false
     ? "<input type='button' onclick='addRow_$u();' value='".$this->settings['add']."'>"
     : '' )
   .( $this->settings['debug'] !== false
     ? "<input type='button' onclick='debug_$u();' value='debug ".$u."'>"
     : '' )
   . '<!--ft_'.$u.'-->
');
 }
 
}//




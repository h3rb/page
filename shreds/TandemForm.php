<?php

class TandemForm extends Unique {

 public $id,$settings,$rowforms,$data,$added;

 public $boundary="#b#b#b#", $subboundary="#l#l#l#";

 public function TandemForm ( $s ) {
  $this->Uniqueness();
  $this->settings=array();
  $this->Set($s);
 }

 public function Set( $s ) {
  if ( !isset($s['remove'])  ) $s['remove'] = true;
  if ( !isset($s['forms'])   ) echo 'Warning: no forms provided!';
  if ( !isset($s['data'])    ) $s['data']=array();
  if ( !isset($s['readonly'])) $s['readonly']=false;
  if ( !isset($s['css'])     ) $s['css']="form";
  if ( !isset($s['ratio'])   ) $s['ratio']=0.33;
  if ( !isset($s['delete'])  ) $s['delete']="Remove";
  if ( !isset($s['link'])    ) $s['link']=false;
  if ( !isset($s['separator']))$s['separator']='&nbsp;&nbsp;&nbsp;';
  if ( !isset($s['label']) )   $s['label']='<b>Add an item:</b><br>';
  if ( !isset($s['header']) )  $s['header']='<div style="width:auto; background-color:#AFA"><b>Selected Items:</b></div>';
  if ( !isset($s['left']) )    $s['left']='tf_left';
  if ( !isset($s['right']) )   $s['right']='tf_right';
  if ( !isset($s['item']) )    $s['item']='tf_item';
  $this->settings=$s;
 }

 // Utility: Recursive JS array building
 public function JSArray( $symbol, $values, $nestedindices=false ) {
  if ( $symbol!==false ) $js='var '.$symbol.'_'.$this->id.'=new Array(';
  else $js=='new Array(
';
  $t=count($values);
  if ( is_array($nestedindices) ) {
   $indices=$nestedindices;
   $sub=array_shift($indices);
  } else {
   $sub=$nestedindices;
   $indices=false;
  }
  foreach ( $values as $v ) { $t--;
   $js.='
 ';
   if ( $sub !== false ) $val=$v[$sub]; else $val=$v;
   if ( $val===true
     || $val===false ) $js.=($val===true?"true":"false").($t>0?',':'');
   else if ( is_numeric($val)
          || $val == "true"
          || $val == "false" ) $js.= $val.($t>0?',':'');
   else if ( is_array($val) ) {
    $js.=$this->JSArray(false,$val,(!is_array($indices)||count($indices)==0?false:$indices)).($t>0?',':'');
   } else $js.="'".$val."'".($t>0?',':'');
  }
  $js.='
)'.($symbol!==false?';':'').'
';
 return $js;
 }

 public function bind( $arrayz ) {
  $one=array();
  foreach ( $arrayz as $e=>$r ) {
   if ( is_array($r) ) {
    $two=array();
    foreach ( $r as $k=>$v ) {
     $two[]=$k;
     $two[]=$v;
    }
    $one[]=$e;
    $one[]=implode($subboundary,$two);
   } else {
    $one[]=$e;
    $one[]=$r;
   }
  }
  return implode($boundary,$one);
 }

 public function unbind( $str ) {
  $result=array();
  $one=explode($boundary,$str);
  $last=false;
  foreach ( $one as $v ) {
   if ( $last === false ) {
    $last=$v;
   } else {
    if ( strpos($subboundary, $v) !== FALSE ) {
     $result[$last]=explode($subboundary,$v);
    } else {
     $result[$last]=$v;
    }
    $last=false;
   }
  }
  return $result;
 }

 // Compacts form data from URI params
 // vector : tf_tform_datereq_form1_1_0
 public function GetParams( $request, $id ) {
  $id=intval($id);
  $result=array();
  foreach ( $request as $name=>$value ) if ( $name[2]!="form" && intval($names[4])==$id ) {
   $names=explode('_',$name);
   if ( $names[0] == 'tf' && $names[1] == 'tform' ) {
    $i=intval($names[5]);
    if ( !isset($result[$i]) ) $result[$i]=array();
    $result[$i][$names[2]]=urldecode($value);
    $result[$i]['form']=$names[3];
   }
  }
  return $result;
 }

 public function Tag( $view, $item, $form, $u, $n, &$rowjs, $in=false ) {
//  echo 'in=';var_dump($in);
  if ( $in===false ) $in=array();
//  $rowjs='';
  $tag='';
  $fc=$this->settings['css'];
  $l='';
  $e='';
  if ( isset($in['disabled']) && $in['disabled'] === true ) $r=true;
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
     }
    }

   $id='tf_tform_'.$f.'_'.$u.'_'.$n;
   $value=(isset($in[$f])?$in[$f]:$e);
   $width=(isset($w)?$w:20);
   $height=(isset($h)?$h:5);
   $class=$fc.'_'.$i;
   $j='javascript:'.$j.'(this);';

  if ( !isset($i) || (!isset($i) && (isset($r) && $r === true) )) {
    $tag='<span class="data">'.(isset($in[$f])?$in[$f]:'').'</span>';
    $tag=(isset($l)?'<span class="'.$fc.'_label">'.$l."</span>":'').$tag.(isset($c)?'<span class="'.$fc.'_caption'.'">'.$c.'</span>':'');
  } else if ( isset($i) ) {
      if ( isset($r) && $r === true ) $r=($i=="checkbox"||$i=="button"?'disabled ':'readonly '); else $r='';
      if ( $i == "hidden" ) {
       $tag='<input '.$js.' id="'.$id.'" type="hidden" name="'.$l.'" '.$r.'value="'.$value.'">';
      } else
      if ( $i == "text" ) {
       $tag='<input '.$js.' class="'.$class.'" type="text" width="'.$width.'" '.$r.'value="'.$value.'" '
           .(isset($j)?'onblur="'.$j.'" ':'')
           .'id="'.$id.'">';
      } else
      if ( $i == "textarea" ) {
       $tag='<textarea '.$js.' class="'.$class.'" id="'.$id.'" '.$r
           .(isset($j)?'onblur="'.$j.'" ':'')
           .'cols="'.$width.'" rows="'.$height.'">'.(isset($in[$f])?$in[$f]:'').'</textarea>';
      } else
      if ( $i == "date" ) {
       $tag='<input '.$js.' id="'.$id.'" class="'.$class.'" type="text" '.$r.'value="'.(strlen($value)>0?date('m/d/Y',strtotime($value)):'').'" width="'.$width.'" onBlur="javascript:validDate(this);">';
       if ( is_numeric($n) || is_int($n) ) {
        if ( !(isset($in['disabled']) && $in['disabled']===true))
        $view->jQuery()->addonload('$(function(){$("#'.$id.'").datepicker({changeMonth:true,changeYear:true});});');
       }
       else if ( !(isset($in['disabled']) && $in['disabled']===true) ) $rowjs.='$("#'.$id.'").datepicker({changeMonth:true,changeYear:true});';
      } else
      if ( $i == "datetime" ) {
       $tag='<input '.$js.' id="'.$id.'" class="'.$class.'" type="text" '.$r.'value="'.(isset($in[$f])?date('r',strtotime($value)):'').'" width="'.$width.'" onBlur="javascript:validDate(this);">';
       if ( is_numeric($n) || is_int($n) ) $view->jQuery()->addonload('$(function(){$("#'.$id.'").datepicker({changeMonth:true,changeYear:true});});');
       else $rowjs.='$("#'.$id.'").datepicker({changeMonth:true,changeYear:true});';
      } else
      if ( $i == "zip" ) {
       $tag='<input '.$js.' class="'.$class.'" '
           .(isset($j)?'onblur="'.$j.'" ':'')
           .'type="text" value="'.$value.'" '.$r.'id="'.$id.'" width="'.(isset($w)?$w:5).'">';
      } else
      if ( $i == "phone" ) {
       if ( !isset($m) && (is_numeric($n) || is_int($n)) ) $m="(999)999-9999 ?x99999";
       $tag='<input '.$js.' id="'.$id.'" class="'.$class.'" type="text" value="'.$value.'" width="'.$width.'" maxlength="'.$width.'">';
       if ( is_numeric($n) || is_int($n) ) $view->jQuery()->addonload('$(function(){$("#'.$id.'").keypad({showOn:"focus",keypadOnly:false});});');
       else $rowjs.='$("#'.$id.'").keypad({showOn:"focus",keypadOnly:false});';
      } else
      if ( $i == "checkbox" ) {
       if ( !isset($e) || strlen($e)==0 ) $e=0;
       $tag='<input '.$js.' class="'.$class.'" type="checkbox" '.$r.'value="'.$value.'" '
           .(isset($j)?'oncheckbox="'.$j.'" ':'')
           .'id="'.$id.'"'.(intval($value)==1?' checked':'').'>';
      }
    if ( is_numeric($n) || is_int($n) ) {
     if ( isset($m) && $m!== false ) $rowjs.='$("#'.$id.'").mask("'.$m.'",{placeholder:" "});';
    }
    $tag=($i!="hidden"&&isset($l)?'<span class="'.$fc.'_label">'.$l."</span>":'').$tag.(isset($c)?'<span class="'.$fc.'_caption'.'">'.$c.'</span>':'');
   }
  return $tag;
 }

 public function Form($view, $n,$item, $name, $scaf, &$rowjs, $in=false ) {
  $form='<td class="'.$this->settings['item'].'"><div class="'.$this->settings['css'].'_item">'.$item.'</div>';
  //echo 'scaf=';var_dump($scaf);
  foreach ( $scaf as $col ) {
   //echo 'col=';var_dump($col);
   if ( is_array($col) ) $form.=$this->Tag($view,$item,$col,$name.'_'.$this->id,$n,$rowjs,$in).$this->settings['separator'];
   else $form.=$col;
  }
  $form.='</td><td>'.( $this->settings['link']===true  ? '<a href="javascript:tf_delete_'.$this->id.'(this);">'.$this->settings['delete'].'</a>'
           : '<input type="button" onclick="javascript:tf_delete_'.$this->id.'(this);" value="'.$this->settings['delete'].'">' )
        .'<input type="hidden" name="form" value="'.$name.'" id="tf_tform_form_'.$n.'_'.$this->id.'">'
        .'</td>';
  return $form;
 }

 // Renders the Tandem Form
 // both the selector (left) and the target list (right)
 public function render( $view, &$html ) {
  $u=$this->id;
  $left=intval($this->settings['ratio'] * 100);
  $right=100-$left;
  $js='';
  $html='';
  // Renders each form type and store the crystalized version inside a js array
  $i=0;
  $items=array(); // holds the form's names for the left-hand selector
  $scaffolds=array(); // holds the form scaffolds
  foreach ( $this->settings['forms'] as $name=>$scaf )  {
   $items[$name]=$scaf['name'];
   unset($scaf['name']);
   $scaffolds[$name]['array']=$scaf;
   $scaffolds[$name]['js']='';
   $scaffolds[$name]['html']=$this->Form($view, '###', $items[$name], $name, $scaf, $scaffolds[$name]['js'], false );
   $scaffolds[$name]['html']=str_replace( "'", "\\'", $scaffolds[$name]['html']);
   $i++;
  }
  $js.=$this->JSArray( 'tf_forms', $scaffolds, 'html' );
  $js.=$this->JSArray( 'tf_scripts', $scaffolds, 'js' );
  $js.='var tf_rows_'.$u.'='.($rows=count($this->settings['data'])).';
var tf_items_'.$u.'='.($item_count=count($items)).';
';
  // provide repAll, addForm, rowin_ tf_delete and tandemform_ (which returns everything escaped into a long string)
  $js.="function repAll_$u(x,y,z) { var s=x; var i=s.indexOf(y); while (i != -1){ s=s.replace(y,z); i= s.indexOf(y); } return s; }
function addForm_$u(o) {
 var f=parseInt(o.value); if ( f<0 || f>tf_forms_".$u.".length ) return;
 var t=document.getElementById('tf_right_".$u."');
 var r=t.insertRow(tf_rows_".$u.");
 r.id='tf_row_'+tf_rows_".$u."+'_".$u."';
 r.innerHTML=repAll_".$u."(tf_forms_".$u."[f],'###',tf_rows_".$u.");
 setTimeout(repAll_".$u."(tf_scripts_".$u."[f],'###',tf_rows_".$u."),1);
 ++tf_rows_".$u.";
}
function rowin_$u(o) { var node = o; while (node && node.tagName != 'TR') { node = node.parentNode } return node; }
function tf_delete_$u(n) {
 var t=document.getElementById('tf_right_$u');
 var d=rowin_$u(n);
 t.deleteRow(d.rowIndex);
 tf_rows_".$u."--;
 var rows=t.rows;
 for ( var i=0; i<tf_rows_".$u."; i++ ) {
  var r=rows[i];
  r.id='tf_row_'+(i)+'_$u';
 }
}
function tandemform_$u() {
 var ele=$(\"[id^='tf_tform_']\");
 var out='';
 for ( var i=0; i<ele.length; i++ ) {
  var e=ele[i];
  out+='&'+e.id+'='+escape(e.value);
 }
 return out;
}
";
  
  $html.='<table width="100%"><tr><td width="'.$left.'%" valign="bottom" class="'.$this->settings['left'].'">
'.$this->settings['label'];
  // Populate a list (left) that responds to clicks and populates the right side using js
  $html.='<select type="list" size="'.$item_count.'" width="100%" id="'.$u.'" onclick="javascript:addForm_'.$u.'(this);">
';
  $i=0;
  foreach ( $items as $n=>$verbose ) {
   $html.='<option value="'.$i.'">'.$verbose.'</option>';
   $i++;
  }
  $html.='</select>';
  $html.='
</td><td width="'.$right.'">'
.$this->settings['header'].
'<table width="100%" id="tf_right_'.$u.'" class="'.$this->settings['right'].'">
';
  // Populate the right-side list table with the existing data and associate forms
  $i=0;
  foreach ( $this->settings['data'] as $data ) {
   $rowjs='';
//   echo 'data=';var_dump($data);
   $html.='
<tr id="tf_row_'.$i.'_'.$u.'">'.$this->Form($view,$i,$items[$data['form']],$data['form'],$scaffolds[$data['form']]['array'],$rowjs,$data).'</tr>
';
   $js.=$rowjs;
   $i++;
  }
  $html.='</table></td></tr></table>';
  
  // Provide tandemform_id() function for returning data.
  return $js;
 }

}//


<?php

 class HPropertyDefinition {
  var $name,$type,$starting,$singular,$protected,$reference,$list,$element,$abstract;
  function HPropertyDefinition( $k, $t, $d=NULL ) {
   $this->name=$k;
   $this->type=$t;
   $this->starting=$this->DefaultByType($d);
   $this->singular=$this->isSingular();
   $this->protected=$this->isProtected();
   $this->reference=$this->isReference();
   $this->list=$this->isList();
   $this->abstract=$this->isAbstract();
   $this->element=$k;
  }
  function is( $a, $b ) { // needle, haystack
   if ( stripos($b,",") === FALSE ) return strcasecmp($a,$b)==0 ? TRUE : FALSE;
   else {
    $o=explode(",",$b);
    $matches=0;
    foreach ( $o as $k ) if ( strcasecmp($a,$k)==0 ) {
     $matches++;
     break;
    }
    return $matches > 0 ? TRUE : FALSE;
   }
  }
  function matches( $key ) {
   return $this->is($key,$this->name) ? TRUE : FALSE;
  }
  function DefaultByType( $value ) {
   if ( $value == NULL || is_null($value) ) {
    if ( $this->isList() ) return array();
    if ( $this->Typed("string") ) return "";
    if ( $this->Typed("axiom") ) return FALSE;
    if ( $this->Typed("integer") ) return 0;
    if ( $this->Typed("decimal") ) return "0.0";
    return NULL;
   } else return $value;
  }
  function Prefixed( $v ) { return (stripos($this->type,$v.":") !== FALSE) ? TRUE : FALSE; }
  function Postfix() {
   $parts=explode(":",$this->type);
   if ( count($parts) < 2 ) {
    echo 'HPropertyDefinition: PARSE ERROR in property classtype `'.$t.'` thought as class or list ';
    /*var_dump($this);*/
    die;
   }
   return $parts[1];
  }
  function Typed( $t ) { return $this->is($t,$this->type); }
  function isSingular() {
   if ( $this->Typed("axiom") ) return TRUE;
   return FALSE;
  }
  function isProtected() {
   if ( $this->Typed("string") ) return TRUE;
   if ( $this->Typed("protected") ) return TRUE;
   return FALSE;
  }
  function isReference() {
   if ( $this->Prefixed("class") ) return TRUE;
   return FALSE;
  }
  function isList() {
   if ( $this->Prefixed("list") ) return TRUE;
   return FALSE;
  }
  function isAbstract() {
   return $this->isReference() == TRUE || $this->isList() == TRUE ? TRUE : FALSE;
  }
  function Assign( $classes, $input ) {
   if ( $this->list == TRUE ) {
    $classname=$this->Postfix();
    return new HDataPoint( $classes, $classes->Find($classname), $input );
   } else if ( $this->reference == TRUE ) {
    $classname=$this->Postfix();
    return new HDataPoint( $classes, $classes->Find($classname), $input );
   } else {
    return $input;
   }
  }
 };

 class HClassDefinition {
  var $name;
  var $property;
  function HClassDefinition( $n, $hvars=FALSE ) {
   $this->name=$n;
   $this->property=Array();
   if ( $hvars !== FALSE ) {
    foreach ( $hvars as $hvar ) Add($hvar);
   }
  }
  function Add( $hvar ) {
   $this->property[]=$hvar;
  }
  function Find( $key ) {
   foreach ($this->property as $p) if ( $this->is($key,$p->name) ) return $p;
   return FALSE;
  }
  function isList() {
   $result= ( count($this->property) >= 1 && $this->property[0]->isList() ) ? TRUE : FALSE;
//   if ( $result === TRUE ) $this->property[0]->element = $this->name;
   return $result;
  }
  function is( $a, $b ) { // needle, haystack
   if ( stripos($b,",") === FALSE ) return (strcasecmp($a,$b)==0) ? TRUE : FALSE;
   else {
    $o=explode(",",$b);
    $matches=0;
    foreach ( $o as $k ) if ( strcasecmp($a,$k)==0 ) {
     $matches++;
     break;
    }
    return $matches > 0 ? TRUE : FALSE;
   }
  }
  function matches( $key ) {
   $key=str_replace("list:","",$key);
   $key=str_replace("class:","",$key);
   return $this->is($key,$this->name) ? TRUE : FALSE;
  }
 };

 class HClassDefinitions {
  var $list,$version;
  function HClassDefinitions( $version ) {
   $this->list=array();
   $this->version=$version;
   $this->CreateDefinitions();
  }
  function Add( $name, $hvars, $isRoot=FALSE ) {
   $class=new HClassDefinition($name);
   foreach ($hvars as $hvar) $class->Add($hvar);
   $this->list[]=$class;
  }
  function Prefixed($key) { return (stripos($key,":") !== FALSE) ? TRUE : FALSE; }
  function Postfix($key) {
   $parts=explode(":",$key);
   if ( count($parts) < 2 ) {
    echo 'HClassDefinitions: ERROR in Find: classtype `'.$t.'` thought as class or list ';
    /*var_dump($this);*/
    die;
   }
   return $parts[1];
  }
  function Find( $key ) {
   if ( $this->Prefixed($key) ) $key=$this->Postfix($key);
   //echo $key.PHP_EOL;
   foreach ( $this->list as $class ) if ( $class->matches($key) == TRUE ) return $class;
   return FALSE;
  }
  function CreateDefinitions() {
   $line_number=1;
   $file=explode("\n",str_replace("\r","",( $this->version )));
   $lines=array();
   foreach ( $file as $line ) {
    if ( stripos(trim($line),'#') === 0 ) continue; // # comments
    $parts = explode(' ',$line);
    $word=array();
    foreach( $parts as $part ) { $trimmed=trim($part); if ( strlen($trimmed) > 0 ) $word[]=$trimmed; }
    $words=count($word); if ( $words == 0 ) continue;
    $lines[$line_number]=$word;
    $line_number++;
   }
   $defining=FALSE;
   $definition=array();
   $defined=array();
   foreach ( $lines as $line_number=>$word ) {
    $words=count($word);
    if ( $words == 2 ) {
     if ( strcasecmp($word[0],"class") == 0 ) {
      if ( $defining !== FALSE ) {
       $defined[$defining]=$definition;
       $defining=$word[1];
       $definition=array();
      } else {
       $defining=$word[1];
       $definition=array();
      }
     } else {
      $definition[$word[0]]=$word[1];
     }
    } else if ( $words == 3 ) {
     $definition[$word[0]]=array($word[1],$word[2]);
    } else {
     echo 'HClassDefinitions: encountered a bad line ('.$words.' words) '.$line_number.' <<< '.$line;
     die;
    }
   }
   if ( $defining !== FALSE ) {
    $defined[$defining]=$definition;
   }
   foreach ( $defined as $classname=>$property_array ) {
    $properties = array();
    foreach ( $property_array as $name=>$type ) {
     if ( is_array($type) ) $properties[$name]=new HPropertyDefinition($name,$type[0],$type[1]);
     else $properties[$name]=new HPropertyDefinition($name,$type);
    }
    $this->Add($classname,$properties);
   }
  }

 };

 class HDataStream {
  var $input,$pointer,$length;
  var $arg1;
  function HDataStream( $in ) {
   $this->input=$in;
   $this->pointer=0;
   $this->length=strlen($this->input);
  }
  function End() { return $this->pointer < $this->length ? FALSE : TRUE; }
  function Next() { $this->pointer=$this->string_argument( $this->pointer, $this->input ); return $this->arg1; }
  function _FILLER($c) { return ( $c === ' ' || $c === ',' || $c === '=' || $c === "\n" || $c === "\r" || $c === "\t" ); }
  function _SEP($c) { return ( $c === "'" || $c === '"' ); }
  function _NESTERS($c) { return ( $c === '{' || $c === '[' || $c === '(' ); }
  function _NESTERE($c) { return ( $c === '}' || $c === ']' || $c === ')' ); }
  function char_in( $c, $list ) { $O=strlen($list); for ( $o=0; $o<$O; $o++ ) if ( $list[$o] === $c ) return TRUE; return FALSE; }
  function string_argument( $arg, $argument ) {
   $cEnd = ' '; $this->arg1="";
   // Advance past spaces and interim commas, equal signs, newlines, skip #comments
   while ( isset( $argument[$arg]) && ($this->_FILLER($argument[$arg]) || $argument[$arg] === '#') ) { if ( $argument[$arg] === '#' ) { while ( $argument[$arg] !== '\n' && $argument[$arg] !== '\r' && $arg < strlen($argument) ) $arg++; } else $arg++; }
   // Handle nested {} [] (), or quotes "" '' ``
   if ( isset($argument[$arg]) && ( $this->_NESTERS($argument[$arg]) || $this->_SEP($argument[$arg]) ) ) { $nests=1; $cStart=$argument[$arg]; $arg++; switch ( $cStart ) { case '{': $cEnd = '}'; break;  case '[': $cEnd = ']'; break; case '(': $cEnd = ')'; break;  case "'": $cEnd = "'"; break; case '"': $cEnd = '"'; break; case '`': $cEnd = '`'; break; } while ( $arg < strlen($argument) && $nests > 0 ) { if ( $argument[$arg] === $cEnd[0] ) { $nests--; if ( $nests == 0 ) break; } else if ( $argument[$arg] === $cStart[0] ) $nests++; $this->arg1.=($argument[$arg]); $arg++; } $arg++; } else { while ( $arg < strlen($argument) ) { if ( $this->char_in( $argument[$arg], "\n\r[{(,= " ) === TRUE ) break; $this->arg1.=$argument[$arg]; $arg++; } }
   // Advance past spaces and interim commas, equal signs, newlines, skip #comments
   while ( isset($argument[$arg]) && ( $this->_FILLER($argument[$arg]) || $argument[$arg] === '#' ) ) { if ( $argument[$arg] === '#' ) { while ( $argument[$arg] !== '\n' && $argument[$arg] !== '\r' && $arg < strlen($argument) ) $arg++; } else $arg++; }
   return $arg;
  }
  function pop_first( $place, $string, &$out ) { $place=$this->string_argument($place,$string); $out=$this->arg1; return $place; }
  function count_keys ( $in ) { $total=0; $result=array(); $length=strlen($in); $place=0; while ( $place < $length ) { $place=$this->pop_first($place,$in,$discard); if ( $discard >= 0 ) $total++; } return $total; }
  function indent( $x ) { $out=""; while ( $x > 0 ) { $x--; $out.=" "; } return $out; }
  function asArray( $in ) { $d=new HDataStream($in); return $d->toArray(); }
  function toArray() {
   $out=array();
   while ( !$this->End() ) {
    $out[]=$this->Next();
   }
   return $out;
  }
  function asKV( $in ) { $d=new HDataStream($in); return $d->toKV(); }
  function toKV()  {
   $out=array();
   $data=$this->toArray();
   $t=count($data);
   for ( $i=0; $i<$t; $i+=2 ) {
    $out[$data[$i]]=$data[$i+1];
   }
   return $out;
  }
 };

 class HDataPoint {
  var $input,$type,$contents,$classes,$wrapper;
  function HDataPoint( $classes, $class, $parse ) {
   $this->classes=$classes;
   $this->type=$class;
   $this->input=$parse;
   $this->contents=array();
   $this->wrapper=FALSE;
   foreach ( $class->property as $p ) {
    $this->contents[$p->name]=$p->starting;
   }
   $this->Parse($parse);
  }
  function Parse() {
   $stream=new HDataStream($this->input);
   $word="";
   while ( $stream->End() == FALSE ) {
    $word=$stream->Next();
    if ( strlen(trim($word)) == 0 ) break;
    $matched = $this->type->Find($word);
    if ( $matched == FALSE ) {
     echo 'HDataPoint PARSE ERROR at '.$stream->pointer.'/'.$stream->length
          .': Unknown keyword: '.$word.' could not match in class: '.$this->type->name;
     //var_dump($this->type);
     die;
    } else { // Perform a type-based assignment
     if ( $matched->singular != FALSE ) {
      $this->contents[$matched->name]=TRUE;
     } else {
      $word=$stream->Next();
      if ( $matched->isReference() ) {
       $this->contents[$matched->name] = $matched->Assign($this->classes,$word);
      } else if ( $matched->isList() ) {
       $element=$matched->Assign($this->classes,$word);
       $element->element = $matched->element;
       $this->contents[$matched->name][] = $element;
      } else {
       $this->contents[$matched->name] = $word;
      }
     }
    }
   }
  }
  function toArray() {
   $a=array();
   foreach ( $this->contents as $name=>$value ) {
    if ( is_array( $value ) ) { // lists
     $a[$name]=array();
     foreach ( $value as $numbered=>$object ) {
      $a[$name][]=$object->toArray();
     }
    } else if ( is_object( $value ) ) $a[$name]=$value->toArray(); // references
    else $a[$name]=$value; // direct value
   }
   return $a;
  }
  function toString() {
   $out="";
   foreach ( $this->contents as $name=>$value ) {
    if ( is_array($value) ) {
     foreach ( $value as $numbered=>$object ) {
      $out.=$name.' {'.$object->toString().'} ';
     }
    } else if ( is_object($value) ) $out.=$name.' {'.$value->toString().'} ';
    else $out.=$name.' {'.$value.'} ';
   }
   return $out;
  }
 };

 class HDataDefinition {
  var $name,$root;
  function HDataDefinition($name) {
   $this->name=$name;
   $this->root=array();
  }
  function Add( $keyword, $typedef ) {
   $this->root[$keyword]=$typedef;
  }
  function matches( $classes, $keyword ) {
   foreach ( $this->root as $named=>$type ) {
    if ( strcasecmp($named,$keyword) == 0 ) return $classes->Find($type);
   }
   echo 'HDataDefinition: PARSE ERROR could not match class key `'.$keyword.'`';
   return FALSE;
  }
 };


 class HDataSet {
  var $definition,$classes,$data,$input;
  function HDataSet( $version, $hdatadef, $input ) {
   $this->input=$input;
   $this->definition=$hdatadef;
   $this->classes=new HClassDefinitions($version);
   $this->data=array();
   $this->Parse();
  }
//  function Assign( $breadcrumb, $value ) {
//  }
  function Parse() {
   $stream = new HDataStream($this->input);
   $word="";
   while ( $stream->End() == FALSE ) {
    $word=$stream->Next();
    $wrapper=$word;
    $matched = $this->definition->matches($this->classes,$word);
    if ( $matched == FALSE ) {
     echo 'HDataSet PARSE ERROR at '.$stream->pointer.'/'.$stream->length
          .': Unknown class key: `'.$word.'` could not match in definition `'.$this->definition->name.'` ';
     //$word=$stream->Next();
     //echo 'VALUE: ';
     //var_dump($word);
     //echo ' matched = ';
     //var_dump($matched);
     //var_dump($this->definition);
     //var_dump($this->classes->list);
     die;
    } else {
     if ( $matched->isList() ) {
      $word=$stream->Next();
      $result=new HDataPoint( $this->classes, $matched, $word );
      $result->wrapper=$wrapper;
      $this->data[]=$result;
     } else {
      $word=$stream->Next();
      $this->data[]=new HDataPoint( $this->classes, $matched, $word );
     }
    }
   }
  }
  function toArray() {
   $a=array();
   foreach ( $this->data as $dp ) {
    $a[]=$dp->toArray();
   }
   return $a;
  }
  function toString() {
   $out="";
   foreach ( $this->data as $dp ) {
    if ( $dp->wrapper !== FALSE )
     $out.= $dp->wrapper.' {'.$dp->toString().'} ';
    else $out.=$dp->toString();
   }
   return $out;
  }
 };

 class HDataDictionary {
  var $definitions,$data,$protocol_definition,$version,$protocol;
  function HDataDictionary( $version, $protocol ) {
   $this->definitions=array();
   $this->data=array();
   $this->arrays=array();
   $this->version=$version;
   $this->protocol=$protocol;
   $this->CreateDefinitions();
  }
  function Add( $name, $keyword_type_array ) {
   $def=new HDataDefinition($name);
   foreach ( $keyword_type_array as $key=>$type ) {
    $def->Add($key,$type);
   }
   $this->definitions[$name]=$def;
  }
  function CreateDefinitions() { // TODO: Add to definitions file(s)
   $line_number=1;
   $file=explode("\n",str_replace("\r","",( $this->protocol )));
   $lines=array();
   foreach ( $file as $line ) {
    if ( stripos(trim($line),'#') === 0 ) continue; // # comments
    $parts = explode(' ',$line);
    $word=array();
    foreach( $parts as $part ) { $trimmed=trim($part); if ( strlen($trimmed) > 0 ) $word[]=$trimmed; }
    $words=count($word); if ( $words == 0 ) continue;
    $lines[$line_number]=$word;
    $line_number++;
   }
   $defining=FALSE;
   $definition=array();
   $defined=array();
   foreach ( $lines as $line_number=>$word ) {
    $words=count($word);
    if ( strcasecmp($word[0],"request") == 0 && $words > 1 ) {
     array_shift($word);
     if ( $defining !== FALSE ) {
      $defined[$defining]=$definition;
      //echo 'Defined: '.$defining.PHP_EOL;
      $defining=implode(' ',$word);
      $definition=array();
       //echo 'Defining: '.$defining;
     } else {
      $defining=implode(' ',$word);
      $definition=array();
      //echo 'Defining: '.$defining;
     }
    } else {
     $definition[$word[0]]=$word[1];
    }
   }
   if ( $defining !== FALSE ) {
    $defined[$defining]=$definition;
   }
   foreach ( $defined as $request_name=>$associative_data ) {
    $this->Add($request_name,$associative_data);
   }
  }
  function Find( $key ) {
   foreach ( $this->definitions as $name=>$dd ) if ( strcasecmp($name,$key) == 0 ) return $dd;
   return FALSE;
  }
  function Execute( $def_name, $input_string ) {
   $definition=$this->Find($def_name);
   if ( $definition == FALSE ) {
    echo 'HDataDictionary: bad definition name: `'.$def_name.'` could not match ';
    //var_dump($this);
    die;
   }
   $result=new HDataSet( $this->version, $definition, $input_string );
   $this->data[]=$result;
   return $result;
  }

  function array_to_keys( $parsed, $parent="", $indent=0 ) {
   if ( !is_array($parsed) ) return $parsed;
   $out="";
   foreach ( $parsed as $ordered=>$kv ) {
    $key=$kv[0];
    $value=$kv[1];
    $out.=$this->indent($indent).$key." ";
    if ( $value === TRUE ) {} else {
     $out.="{";
     if ( is_array($value) ) $out.=$this->array_to_keys($value,$key,$indent+1);
     else $out.=$value;
     //$noIndent=$this->isInterior($parent,$key) || $this->isGeneric($key);
     //if ( !$noIndent ) $out.="\n".$this->indent($indent);
     $out.="}\n";
    }
   }
   return $out;
  }

 };

// Test:
/*
 $input_string = '
layouts { layout {
 name {Default} description {} camera {name {} lap { near 0.10000 
 far 1000.00000 
 screen {
 }
 eye {
x 0.00000 y 0.00000 z 200.00000 }
 center {
x 0.00000 y 0.00000 z 0.00000 }
 up {
x 0.00000 y 1.00000 z 0.00000 }
} position {} rotation {}}parts {}
texts {}
stencils {}
lithos {}
 }'.'
 layout {
 hidden name {Layout 2} description {} camera {name {} lap { near 0.10000 
 far 1000.00000 
 screen {
 }
 eye {
x 0.00000 y 0.00000 z 200.00000 }
 center {
x 0.00000 y 0.00000 z 0.00000 }
 up {
x 0.00000 y 1.00000 z 0.00000 }
} position {} rotation {}}parts {}
texts {}
stencils {}'.'
lithos {litho {name {Litho 1} description {Design Litho} transform {position {x 0.00000 y 0.00000 z 7.00000} scale {x 10.00000 y 10.00000 z 3.00000}  rotation {x 0.00000 y 0.00000 z 0.00000}} previewTransform {position {x 0.00000 y 0.00000 z 0.00000} scale {x 1.00000 y 1.00000 z 1.00000}  rotation {x 0.00000 y 0.00000 z 0.00000}} camera {name {} lap { near 0.10000 
 far 1000.00000 
 screen {
 }
 eye {
x 0.00000 y 0.00000 z 200.00000 }
 center {
x 0.00000 y 0.00000 z 0.00000 }
 up {
x 0.00000 y 1.00000 z 0.00000 }
} position {} rotation {}} floor {0.40000} resolution {x 256 y 256 fx 256.00000 fy 256.00000 } }
}
 }
}
';

$dataDef = new HDataDictionary("Structure_Version1.txt","Requests_Version1.txt");
$result = $dataDef->Execute( "Update Item", $input_string );
$a = $result->toArray();
$string =$result->toString();

echo '-------------------RESULTS:'.PHP_EOL;

var_dump($a);

echo '-------------------RESULTS:'.PHP_EOL;

var_dump($string);
*/

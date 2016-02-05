<?php
/* http://code.google.com/p/font-lawyer
  Copyright (c) 2012 H. Elwood Gilliland III
  _____           _   _
 |  ___|__  _ __ | |_| |    __ ___      ___   _  ___ _ __
 | |_ / _ \| '_ \| __| |   / _` \ \ /\ / / | | |/ _ \ '__|
 |  _| (_) | | | | |_| |__| (_| |\ V  V /| |_| |  __/ |
 |_|  \___/|_| |_|\__|_____\__,_| \_/\_/  \__, |\___|_|
                                          |___/
 Utilities
*/

require_once('settings.php');

global $fl_smashed_cache;

$fl_smashed_cache = str_replace(fl_site_root,'',flcache);

function fllog( $msg ) {
 if ( !file_exists(flerrlogfile) ) @file_put_contents(flerrlogfile,'');
 if (!($fp = fopen(flerrlogfile, 'a')))
  die(date().' FontLaywer: Cannot open log file to append: `'.flerrlogfile.'`, message was: '.$msg);
 fwrite($fp, $msg.FL_FILE_EOL);
 fclose($fp);
}

function flres( $s ) {
 return str_replace("//","/",$s);
}

function is( $a, $b ) {
 return (strcmp(trim($a),trim($b))==0);
}

function fl_depipe( $in ) {
 $arr=array();
 $in=str_replace("\n","",$in);
 $in=str_replace("\r","",$in);
 $ins=explode("|",$in);
 $i=0;
 $w="";
 foreach ( $ins as $v ) {
  if ( strlen(trim($v)) == 0 ) continue;
  if ( $i % 2 == 0 ) {
   $arr[$w]=$v;
  } else $w=$v;
  $i++;
 }
 return $arr;
}

function fl_enpipe( $in ) {
 $out="";
 foreach ( $in as $k=>$v ) {
  $out.=$k.'|'.$v.'|';
 }
 return $out;
}

/*
 * Special format for sprite description files.
 * Each line contains one sprite description.
 * Each line contains a series of key value pairs, each pair separated by |,
 * except the last value which is always the text content.
 * Example line:
 *   foo=bar|bar=baz|This is some text.
 * Understands // as a comment lines.
 */
function fl_depipe_sprite( $in, $filename ) {
 $ins=explode("|",$in);
 $total=count($ins);
 $i=0;
 $out=array();
 foreach ( $ins as $pair ) {
  $pair=trim($pair);
  if ( $pair[0] == '#' ) continue;
  if ( $pair[0] == '/' && $pair[1] == '/' ) continue;
  $pair = explode('//', $pair);
  $pair=$pair[0];
  $i++;
  if ( $i == $total ) {
   $out['text']=str_replace("<br>","\n",htmlspecialchars_decode($pair));
  } else {
   $element=explode('=',$pair);
   $key=trim($element[0]);
   $value=trim($element[1]);
   if ( strlen($key) == 0 ) $key="invalid";
   if ( isset($out[$key]) )
    fllog( $filename . ' Warning on line '.$i.', duplicate key `'.$key.'`' );
   $out[$key]= is_numeric($value) ? floatval($value) : $value;
  }
 }
 return $out;
}

function locate_font( $fontname ) {
 $test=flvault.$fontname;
 if ( file_exists($test) ) return $test;
 return false;
}

function is_valid_folder( $target ) {
/*
 if ($handle = opendir($target)) {
  while (false !== ($entry = readdir($handle))) {
   if ($entry != "." && $entry != "..") {
    if (is_dir($target."/".$entry) === true) return true;
	else return false;
   }
  }
 }
 closedir($handle);
 return false;
 */
 return true;
}

function is_empty_folder($dir) {
 if (($files = @scandir($dir)) && count($files) <= 2) return true;
 return false;
}

function fl_file_time( $file ) {
 $lastModified = @filemtime($file);
 if($lastModified == NULL)
    $lastModified = filemtime(utf8_decode($file));
 return $lastModified;
}

/*
 * This may not work in IIS see posts on php.net re: filemtime()
 */
function file1_is_older( $a, $b ) {
 if ( !file_exists($a) ) return false;
 $result= (fl_file_time($a) > fl_file_time($b));
// fllog( "file1_is_older: ".$a.' is newer than '.$b . '? ' . ( $result ? "yes" : "no" ) );
 return $result;
}

function fl_query_font_render_size( $font, $size, $color, $text ) {
// echo 'Querying for '.$text;
 $d=new ImagickDraw();
 $d->setFont($font);
 $d->setFontSize(intval($size));
 $d->setGravity(Imagick::GRAVITY_CENTER);
 $d->setFillColor($color);
 $c=new Imagick();
 $m=$c->queryFontMetrics($d,$text);
 $c->clear();
 $c->destroy();
 $d->clear();
 $d->destroy();
 $dim=array( 'w'=>$m['textWidth'], 'h'=>$m['textHeight'] );
 return $dim;
}

if ( defined('fl_test_environment') || fl_test_environment !== true ) {

/*
 * Test the environment for operability.
 */

if ( !is_dir(flcache) && is_valid_folder(flcache) === false ) {
 fllog( 'Cache folder `'.flcache.'` does not exist or is inaccessible.' );
} else {
 if ( !is_writeable(flcache) )
 fllog( 'Cache folder `'.flcache.'` cannot be written to.' );
}

if ( !is_dir(flvault) && is_valid_folder(flcache) === false ) {
 fllog( 'Vault folder `'.(flvault).'` does not exist or is inaccessible.' );
} else if ( is_empty_folder(flvault) ) {
 fllog( 'No files are accessible (or contained) in the vault folder `'.vault.'`' );
}

} // fl_test_environment

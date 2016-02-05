<?php

define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'] );

/* http://code.google.com/p/font-lawyer
  Copyright (c) 2012 H. Elwood Gilliland III
  _____           _   _
 |  ___|__  _ __ | |_| |    __ ___      ___   _  ___ _ __
 | |_ / _ \| '_ \| __| |   / _` \ \ /\ / / | | |/ _ \ '__|
 |  _| (_) | | | | |_| |__| (_| |\ V  V /| |_| |  __/ |
 |_|  \___/|_| |_|\__|_____\__,_| \_/\_/  \__, |\___|_|
                                          |___/
 Font Sprite Manager

 This file contains the functions that manage CSS sprite sheets.

 Use:

   fl_get_sheet( "SheetID" ) in your page <head> block to return <link> tag.

   fl_sprite( __FILE__, "SheetID", "SpriteID" ) to return a sprite to the page.  Requires
   a file to be placed in the cache folder called "SheetID.txt" that contains one-liner
   pipe-delimited key-value pairs with a trailing text block that can include some HTML.

     example SheetID.txt in the cache folder:

id=one|color=#00000|size=72|font=verdana|This is the text &amp; html codes!
id=two|color=#FF000|size=36|font=verdana|This is the text &amp; html codes!<br>Newline is br!
id=SpriteID|color=#0000FF|size=24|font=visitor|Woooooot.

    Then use fl_sprite() to return the content to the page.

     or

   fl_sprite( __FILE__, "SheetID", "SpriteID", "div", "png" );
   fl_sprite( __FILE__, "SheetID", "SpriteID", "div", "jpg" );
   fl_sprite( __FILE__, "SheetID", "SpriteID", "div", "gif" );
   fl_sprite( __FILE__, "SheetID", "SpriteID", "span", "png" );

    - When specifying "png", "jpg", "gif' for a "SpritesID", it must be the same across
      all pages for the same SheetID, otherwise sheets will be output in multiple formats.
      To maintain optimization, this is not a feature so use the same type across all
      pages.  Tag is usually "div" or "span"
*/

require_once('utility.php');
require_once('RectPackSimple.php');

function fl_get_sheet_filename( $sheet_id ) {
 return flres(flcache.$sheet_id.'.css');
}

function fl_get_sheet_description_filename( $sheet_id ) {
 return flres(flcache.$sheet_id.'.txt');
}

function fl_get_fontfile( $fontname ) {
 return flres(flvault.$fontname);
}

function fl_get_sheet( $sheet_id ) {
 global $fl_smashed_cache;
 return '<link rel="stylesheet" type="text/css" href="/'
  .flres($fl_smashed_cache.$sheet_id.'.css')
  .'">';
}

function fl_get_css_sprite( $sheet_id, $sprite, $output_file_type="png", $eol=FL_FILE_EOL ) {
 global $fl_smashed_cache;
 $img = flres($fl_smashed_cache.$sheet_id.'.'.strtolower($output_file_type));
// echo '<pre>'; var_dump($sprite); echo '</pre>';
 return
  '.'.$sheet_id.'-'
  .$sprite['id']
  .' { '.$eol
  .'background: transparent url(\''.$img.'\') no-repeat'
  .(intval($sprite['x']) == 0 ? ' 0px ' : ' -'.$sprite['x'].'px')
  .(intval($sprite['y']) == 0 ? ' 0px;' : ' -'.intval($sprite['y']).'px;').$eol
  .' width:'.intval($sprite['w']).'px;'.$eol
  .' height:'.intval($sprite['h']).'px;'.$eol
  .'}'.$eol;
}

function fl_make_sheet( $sheet_id, $sprites, $output_file_type="png" ) {
 $out="";
 foreach ( $sprites as $k=> $sprite ) if ( !is_numeric($k) ) continue; else {
  $out.=fl_get_css_sprite( $sheet_id, $sprite, $output_file_type ).FL_FILE_EOL;
 }
 file_put_contents( fl_get_sheet_filename($sheet_id), $out );
}

function fl_load_sheet( $sheet_id ) {
 $sprite_description=fldescriptions.$sheet_id.'.txt';
 if ( !file_exists( $sprite_description ) ) return array();
 if ( flchmod == true ) @chmod( 755, $sprite_description );
 $in=file_get_contents($sprite_description);
 if ( flchmod == true ) @chmod( 444, $sprite_description );
 $sprites=array();
 $lines=explode(FL_FILE_EOL,$in);
 foreach ( $lines as $line ) {
  $clean=trim(str_replace("\r","",$line));
  if( strlen($clean) > 0 ) $sprites[]=fl_depipe_sprite($clean,$sprite_description);
 }
 if ( !file_exists( vault.$font['name'] ) )
 fllog('Warning: font sprites loaded for unavailable font file '.$font['name']);
 return $sprites;
}

function fl_regenerate_sprite_cache( $sheet_id, $sprites, $out_image_file_type="png" ) {
 if ( defined('fl_test_environment') ) fllog('! fl_regenerate_sprite_cache: '.flcache.$sheet_id.'.'.$out_image_file_type);
 $biggest_w=0;
 $biggest_h=0;
 $rects=array();
 $i=0;
 foreach ( $sprites as &$sprite ) {
  $i++;
  //var_dump( $sprite );
  if ( !isset($sprite['size']) ) $sprite['size']=14;
  if ( !isset($sprite['color']) ) $sprite['color']='#000000';
  if ( !isset($sprite['font']) ) {
   fllog( 'Font not set for sprite id `'.$sheet_id.'` sprite #'.$i );
   continue;
  }
  //var_dump($sprite);
  $sprite['dimension']=fl_query_font_render_size(
   flres(flvault.$sprite['font']),
   intval($sprite['size']),
   $sprite['color'],
   $sprite['text']
  );
  $sprite['w']=$sprite['dimension']['w'];
  $sprite['h']=$sprite['dimension']['h'];
  $w=intval($sprite['w']);
  $h=intval($sprite['h']);
  if ( $w > $biggest_w ) $biggest_w=$w;
  if ( $h > $biggest_h ) $biggest_h=$h;
  $rects[$i-1]=$sprite;
 }
// echo 'Packing rects..';
 $packed=PackRect(
  $rects,
  fl_biggest_w,
  fl_biggest_h,
  $w,
  $h,
  fl_pack_precision,
  fl_pack_precision
 );
 $i=0;
 try {
 $total=count($rects);
 $c=new Imagick();
 //var_dump($packed);
 $c->newImage(
  intval($packed['width']),
  intval($packed['height']),
   "transparent",
   $out_image_file_type
  );
 $css='';
 $d=array();
 foreach ( $packed as $k=> $sprite ) if ( !is_numeric($k) ) continue; else {
  $d=new ImagickDraw();
  $d->setFont(fl_get_fontfile($sprite['font']));
  $d->setFontSize(floatval($sprite['size']));
  $d->setGravity(Imagick::GRAVITY_NORTHWEST);
  $d->setFillColor($sprite['color']);
  $result=$c->annotateImage(
   $d,
   floatval($sprite['x']), floatval($sprite['y']),
   0, $sprite['text']
  );
  $d->clear();
  $d->destroy();
 }
 $c->setImageFormat(strtoupper($out_image_file_type));
 $c->writeImage(flres(flcache.$sheet_id.'.'.$out_image_file_type));
 $c->clear();
 $c->destroy();
 } catch ( Exception $e ) {
  echo $e->getMessage();
 }
 fl_make_sheet( $sheet_id, $packed, $out_image_file_type );
 //die;
}

function fl_sprite( $_file, $sheet_id, $sprite_id, $tag='div', $output_filetype="png" ) {
 $sheet = flcache.$sheet_id.'.'.$output_filetype;
 $sheetfile = fl_get_sheet_filename($sheet_id);
 $descriptions = fl_load_sheet( $sheet_id );
 if ( count($descriptions) == 0 ) {
  fllog('fl_sprite: no descriptions ('.flcache.$sheet_id.'.txt'
   .','.$sheet.','.$sheetfile.') available for sheet_id `'.$sheet_id.'`, so nothing done FILE: `'.$_file.'`');
  fl_regenerate_sprite_cache( $sheet_id, $descriptions, $output_filetype );
  return '<!-- FontLawyer: sprite description not yet available, place in cache dir as '
   .$sheet_id.'.txt; see docs for details on how to make this -->'.PHP_EOL;
 }
 $render=false;
 $out_image_file_type=strtolower($out_image_file_type);
 $cachefile= flcache.$sprite_id.'.'.$out_image_file_type;
 if ( $_file !== false ) {
  if ( $_file === true ) $render=true;
  else
  if ( file1_is_older($cachefile,$_file)
    && file1_is_older($sheetfile,$_file)
    && file1_is_older(fl_get_sheet_description_filename($sheet_id),$_file)
   ) $render=false;
  else $render=true;
 } else $render=true;
 if ( $render === true )
 fl_regenerate_sprite_cache( $sheet_id, $descriptions, $output_filetype );
 return '<'.$tag.' class="'.$sheet_id.'-'.$sprite_id.'"></'.$tag.'>'.PHP_EOL;
}

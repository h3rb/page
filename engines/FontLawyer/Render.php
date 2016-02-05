<?php
/* http://code.google.com/p/font-lawyer
  Copyright (c) 2012 H. Elwood Gilliland III
  _____           _   _
 |  ___|__  _ __ | |_| |    __ ___      ___   _  ___ _ __
 | |_ / _ \| '_ \| __| |   / _` \ \ /\ / / | | |/ _ \ '__|
 |  _| (_) | | | | |_| |__| (_| |\ V  V /| |_| |  __/ |
 |_|  \___/|_| |_|\__|_____\__,_| \_/\_/  \__, |\___|_|
                                          |___/
 Rendering
*/

require_once("utility.php");

/* fl_text_render
 *
 * Used by fl_text(), this is the least efficient method of handling
 * custom fonts as it does not take advantage of spriting optimizations.
 *
 * Renders a "one-off" cacheable font rendering to image on disk in
 * cache folder.  Image is named after ID and image type.
 *
 * The $_file parameter must be literally __FILE__ from the calling
 * file to determine cache freshness.  If set to false, cache is
 * overwritten.
 *
 * $id must be a filename-safe, slug-style unique identifier
 * like "front-page-top-title1"
 *
 * $out_image_file_type sets the target (jpeg, png, gif) and should
 * be lowercase.
 */

function fl_text_render(
 $_file, $id, $text, $fontname, $fontsize, $color="#000000",
 $out_image_file_type="png"
 ) {
 $font=locate_font($fontname);
 if ( $font === false ) {
  fllog( 'fl_text_render: font `'.$fontname.'` not found at `'.flvault.$fontname.'`');
  return false;
 }
 $render=false;
 $out_image_file_type=strtolower($out_image_file_type);
 $cachefile= flcache.$id.'.'.$out_image_file_type;
 if ( $_file !== false ) {
  if ( file1_is_older( $cachefile, $_file ) ) {
   $render=true;
  }
 } else $render=true;
 if ( $render === true ) try{
  $draw = new ImagickDraw();
  $draw->setFont($font);
  $draw->setFontSize(intval($fontsize));
  $draw->setGravity(Imagick::GRAVITY_CENTER);
  $draw->setFillColor($color);
  $canvas = new Imagick();
  $m = $canvas->queryFontMetrics($draw,htmlspecialchars_decode($text));
  $canvas->newImage(
   $m['textWidth'], $m['textHeight'],
   "transparent",
   $out_image_file_type
  );
  $canvas->annotateImage($draw,0,0,0,$text);
  $canvas->setImageFormat(strtoupper($out_image_file_type));
  $canvas->writeImage($cachefile);
  fllog( 'Writing to: '.$cachefile );
  $canvas->clear();
  $canvas->destroy();
  $draw->clear();
  $draw->destroy();
 } catch(Exception $e){
  fllog( 'fl_text_render() Error: ',  $e->getMessage() );
  return false;
 }
 return $cachefile;
}

/*
 * fl_font is a helper function for rendering to the page.
 * It takes parameters similar to fl_text_render() but also
 * requires a 'tag' ; $tag parameter can be set to img, div,
 * span; id is used to generate css which is stored in the cache
 * if tag is not img.  Setting $seo to true generates an invisible
 * div that may help with SEO and is activated by default.  Height
 * and width are set equal to the rendered text size, so you may need
 * to wrap this in another tag to further style the output.
 *
 * Set the Font Lawyer setting 'fl_site_cache_path' to smash the site
 * path from the cache file path.
 *
 * Returns: true if no error, false if error encountered.
 */
function fl_font(
 $_file, $id, $text, $fontname, $fontsize, $color="#000000",
 $tag="img", $seo=true, $out_image_file_type="png"
 ) {
 $out="";
 global $fl_smashed_cache;
 $cached=fl_text_render(
  $_file, $id, $text, $fontname, $fontsize, $color,
  $out_image_file_type
 );
 if ( $cached === false ) return '<!-- fl_text_render error: see log -->';
 if ( $seo === true )
 $seo_tag='<div style="display:none;visibility:hidden;">'.$text.'</div>';
 else $seo_tag='';
 if ( is($tag,'div') ) {
  $d=getimagesize($cached);
  $css='<style>#'.$id
   .' div {background:url(\''
   .$fl_smashed_cache
   .'\') left top;width:'
   .$d[0].'px;height:'
   .$d[1].'px;display:block;float:left;}<style>';
  $out.= $css.'<div class="'.$id.'"></div>';
  return true;
 } else if ( is($tag,'span') ) {
  $d=getimagesize($cached);
  $css='<style>#'.$id
   .' span {background:url(\''
   .$fl_smashed_cache
   .'\') left top;width:'
   .$d[0].'px;height:'
   .$d[1].'px;display:block;float:left;}<style>';
  $out.= $css.'<span class="'.$id.'"></span>';
  return true;
 } else {
 if ( !is($tag,'img') ) {
  fllog('fl_font in file `'.$_file.'`: bad value for $tag parameter, defaulting to img');
 }
 $out.= '<img src="/'
  .($fl_smashed_cache.$id.'.'.$out_image_file_type)
  .'" border="0">'.$seo_tag;
  return $out;
 }
 return '';
}

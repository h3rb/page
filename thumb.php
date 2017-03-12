<?php
 function thumbnailImage($imagePath) {
  $im = new Imagick();
  $im->setBackgroundColor(new ImagickPixel('transparent'));
  $im->readImage(realpath($imagePath));
  $dim=$im->getImageGeometry();
  if ( !isset($_GET['wh']) ) $wh=100; else $wh=intval($_GET['wh']);
  if ( $wh <= 0 ) $wh=100;
//  $aspect=floatval($dim['width'])/floatval($dim['height']);
//  $inverse_aspect=floatval($dim['height'])/floatval($dim['width']);
  $width=$wh;
  $height=$wh;
  $im->setImageFormat("png32");
  $im->thumbnailImage($width,$height, true, true);
  header("Content-Type: image/png");
  echo $im;
 }
 if ( isset($_GET['src']) ) {
  try {
   @thumbnailImage(urldecode($_GET['src']));
  } catch ( Exception $e ) {}
 }


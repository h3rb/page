<?php
/* http://code.google.com/p/font-lawyer
  Copyright (c) 2012 H. Elwood Gilliland III
  _____           _   _
 |  ___|__  _ __ | |_| |    __ ___      ___   _  ___ _ __
 | |_ / _ \| '_ \| __| |   / _` \ \ /\ / / | | |/ _ \ '__|
 |  _| (_) | | | | |_| |__| (_| |\ V  V /| |_| |  __/ |
 |_|  \___/|_| |_|\__|_____\__,_| \_/\_/  \__, |\___|_|
                                          |___/
 Settings
*/

define( 'fl_site_root', SITE_ROOT.'/' );  // <- trailing slash not optional

/*
 * Sets the security that Font Lawyer will use to protect those
 * precious font files.
 * 0 = unlocked, open fonts, can be stored on web
 * 1 = medium security, fonts are locked by chmod when not in use
 * 2 = high security, fonts are unlocked but stored offline 
 *
 * If `flchmod` is set to true, FontLawyer will use chmod to lock
 * and unlock files.  This must be set if using medium security.
 *
 * When using high security, FontLawyer will lock and unlock font
 * sprite cache files not associated with images if flchmod is set.
 * This means users cannot access the files in the cache directory
 * that aren't associated with a page.  This may not be a necessary
 * step, but provides a slightly more precise level of security.
 */
define( 'flsecurity', 2 );
define( 'flchmod', true );

/*
 * Files stored in the vault: .TTF files used by Font Lawyer
 * to generate.  Files can be stored in a web dir only if
 * security is set to 1.
 */
define( 'flvault', '/offline/fonts/' );

/*
 * Files stored in the cache: exported sprites, css files and
 * text files that include information about the sprite.
 *
 * In your cache folder, it is recommended to use Options -Indexes
 * to avoid sprites being access that are generated for administrative
 * sections of a website.
 */
define( 'flcache', fl_site_root.'cache/FontLawyer/' );
define( 'fldescriptions', fl_site_root.'cache/FontLawyer/' );

/*
 * Silent font lawyer output error log.
 */
define( 'flerrlogfile', fl_site_root.'cache/logs/fl-log.txt' );
define( 'FL_FILE_EOL', "\n" );

/*
 * Comment out in production when you've run this once.
 * Causes utility.php to test the environment for issues.
 */

 define( 'fl_test_environment', true );

 /*
  * Rectangle packing settings.
  *
  * fl_pack_precision controls the rate at which the packer extends the
  * containing image size in its repeated tests to fit all rectangles in
  * the same area.
  *
  * fl_biggest_w limits the image rectangle's growth size width
  * fl_biggest_h limits the image rectangle's growth size height
  *
  * Many modern image file formats extend up to the tens of thousands
  * of pixels in one dimension, while GPU throughput still struggles
  * with images larger than 2048 (though it supports up to 4096 and 8192
  * on some systems); we use 2048 as the limit since this is a compromise
  * between performance, disc size and in-memory size.
  *
  * It is of note that other constraints may limit this.  Currently, there
  * is no "auto sprite stitching", for spanning a sprite across multiple
  * images, within Font Lawyer.  This is a planned feature, but is not
  * currently implemented.  fllog() will report issues with sprites not
  * fitting into the desired space, but will not attempt to span the sprites
  * across multiple image bins.  Quite simply, if you see a warning
  * about this you must break the sprite up into multiple sprites because
  * your number of requested images exceeds the availabl space limited by
  * these settings below.
  */
  define( 'fl_pack_precision', 64 );
  define( 'fl_biggest_w', 2048 );
  define( 'fl_biggest_h', 2048 );

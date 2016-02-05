<?php

/**
 * Breadcrumber
 * (c) 2013 H. Elwood Gilliland III
 *
 * Usage:
 *
 * On a breadcrumbed page, you would use
 * breadcrumber() to generate the structured
 * breadcrumb on the breadcrumbed page,
 * and for areas where you need to recall a cached
 * breadcrumb "reprogram", use js_recall_crumbs()
 * which will rewrite various crumbs.
 *
 * Before you reach the breadcrumbed page, you can use
 * js_breadcrumber() to generate a <script></script>
 * containing "hot" breadcrumb overwrites to the store,
 * which will allow you to store your reprogrammed
 * crumbs.  You will need one of these for each
 * reprogrammable crumb, and you will call this from
 * the appropriate event.
 *
 * To clear events, you need to clear your storage using
 * the js_clearcrumbstore() function which generates
 * the appropriate javascript function.
 *
 * You must also use js_crumblock() to generate the
 * locking mechanism so when a link is clicked or another
 * appropriate event situation occurs, the crumbs are
 * locked in their current state.  This should occur
 * somewhere before the other js_* functions on any
 * page that is going to reprogram crumbs.
 */

 function js_crumblock( $funcname="lockcrumbs", $crumblock="crumblock" ) {
  return '<script type="text/javascript">
   var '.$crumblock.'=false;
   function '.$funcname.'() { '.$crumblock.'=true; }
   function un'.$funcname.'() {'.$crumblock.'=false; }
  </script>
';
 }

 function js_clearcrumbstore( $funcname="clearcrumbstore", $prefix="bc", $crumblock="crumblock" ) {
  return '<script type="text/javascript">
   function '.$funcname.'() {
    if ( '.$crumblock.' ) return;
    $.jStorage.set("'.$prefix.'-list",false);
   }
  </script>
';
 }

 /*
  * Nice way to check a string.
  */
 if ( !function_exists('is') ) {
  function is( $a, $b ) {
   return (strpos($a,$b) !== false);
  }
 }

 /* h3
  * Deploys a javascript function to recall crumbing data,
  * and reprogram based on numerical index.
  */
 function js_recall_crumbs( $prefix="bc" ) {
  return '<script type="text/javascript">
  var '.$prefix.'_value=$.jStorage.get("'.$prefix.'-list",false);
  if ( '.$prefix.'_value != false ) {
   var keys= '.$prefix.'_value.split(",");
   var total=keys.length();
   for ( var i=0; i<total; i++ ) {
    var link=$.jStorage.get(keys[i]+"-link");
    var url=$.jStorage.get(keys[i]+"-url");
    $("#'.$prefix.'-"+i).html(
     "<a href=\'"+url+"\' alt=\'"+link+"\' title=\'"+link+"\'>"+link+"</a>"
    );
   }
  }
  </script>
  ';
 }

 /* h3
  * breadcrumber() generates an in-page, partially programmable
  * via jStorage, breadcrumb "tree"  ( root R stem A stem B leaf ),
  * which can be of N length.
  * Caveat: breadcrumb links themselves cannot be programmable
  *
  * @param $breadcrumbs
  * Expects either a non-array (which is returned verbatim),
  * or an array of mixed nodes which can be one of:
  *  a string containing a non-link
  *  an array of one element such that "<link text>"=>"<url target>"
  *  an array of one element such that "<link>"=>"#" (renders as a non-link)
  *  an array of multiple key-value pairs:
  * Type 1)
  *     "link"=>"<value>"
  *     "url"=>"<value>"
  *     "title"=>"<value>"
  *     "alt"=>"<value>"
  * *- there is only one type right now
  */
function breadcrumber(
  $breadcrumbs,
  $cssclass="breadcrumbs",
  $id="breadcrumbs",
  $crumbclassroot="bc-root",
  $crumbclassstem="bc-stem",
  $crumbclassleaf="bc-leaf",
  $crumbidprefix="bc",
  $crumbwraptag="div",
  $crumbtag="span",
  $sepRid="sepr",
  $sepRclass="bc-sepr",
  $sepRcontent=">",
  $sepAid="sepa",
  $sepAclass="bc-sepa",
  $sepAcontent=">",
  $sepBid="sepb",
  $sepBclass="bc-sepb",
  $sepBcontent=">"
 ) {
 if ( !is_array($breadcrumbs) ) return $breadcrumbs;
 $crumbs=count($breadcrumbs);
 if ( $crumbs==0 ) {
  return '<'.$crumbwraptag.' class="'.$cssclass.'" id="'.$id.'"></'.$crumbwraptag.'>';
 }
 $out = '<'.$crumbwraptag.' class="'.$cssclass.'" id="'.$id.'">';
 $crumb=1;
 foreach ( $breadcrumbs as $breadcrumb ) {
  if ( is_array($breadcrumb) ) {
   if ( count($breadcrumb) == 1 ) { // non-programmable link=>url
    foreach ( $breadcrumb as $link=>$url ) {
     $out.='<'.$crumbtag.' id="'.$crumbidprefix.'" class="'.$crumb.'">';
     if ( is($url,"#") ) {
      $out.=$link;
     } else {
      $out.='<a href="'.$url.'" alt="'.$link.'" title="'.$link.'">'.$link.'</a>';
     }
     $out.='</'.$crumbtag.'>';
    }
   } else {
    $out.='<'.$crumbtag.' id="'.$crumbidprefix.'" class="'.$crumb.'">'.$breadcrumb.'</'.$crumbtag.'>';
   }
  }
  $out.='</'.$crumbwraptag.'>';
 }
 return $out;
}

 /*
  * Returns a js callable which changes a set of stored breadcrumb exchanges for
  * reprogrammable breadcrumbs.  This can be called by an event, such as a mouseover
  * on a link.  Access a global javascript variable called breadcrumb_lock which
  * can be set when a link or button is pressed to stop the store from changing.
  *
  * @param $funcname
  * a well-named js function name which can be used later when you form your html
  * or whatever other widget.  it usually is enumerated like bread1,bread2
  * @param $crumbinfo
  * a numerically indexed array of breadcrumb rewrites that contain single element
  * arrays like this:  array( 2=>array( "link"=>"url" ) )
  * where the number corresponds to the breadcrumb we are rewriting.
  * @param $prefix
  * must match the keystore prefix established when you use js_recall_crumbs()
  */
 function js_breadcrumber( $funcname, $crumbinfo, $prefix="bc", $crumblock="crumblock" ) {
  $out= '<script type="text/javascript">
  function '.$funcname.'() {
   if ( '.$crumblock.' ) return;
';
  foreach ( $crumbinfo as $idx=>$crumb ) {
   foreach ( $crumb as $link=>$url ) {
    $out.='$.jStorage.set("'.$idx.'-link","'.$link.'");';
    $out.='$.jStorage.set("'.$idx.'-url","'.$url.'");';
   }
  }
  $out.= '
   var list_setting= $.jStorage.get("'.$prefix.'-list");
   if ( !list_setting )
   $.jStorage.set("'.$prefix.'-list","'.$idx.'");
   else
   $.jStorage.set("'.$prefix.'-list",$.jStorage.get("'.$prefix.'-list")+",'.$idx.'");
  }
 </script>
';
 return $out;
}

<?php

 global $_chartsjs_included; $_chartsjs_included=FALSE;

 global $_chartsjs; $_chartsjs=-1;

 global $_clines; $_clines=0;
 function Line( &$p, $w=400, $h=400, $labels, $datasets, $options=NULL ) {
  global $_chartsjs_included; if ( $_chartsjs_included === FALSE ) { $_chartsjs_included=TRUE; $p->JS('Chart.min.js'); }
  global $_clines; $_clines++;
  global $_chartsjs; $_chartsjs++;
  $id="LineChart".$_clines;
  $js="";
  $js.='var '.$id.'_data={ labels:'.json_encode($labels).', datasets:'.json_encode($datasets).' };   console.log('.$id.'_data);
';
  $js.='var '.$id.'_opts={ '.$options.' };
';
  $js.='var '.$id.'=new Chart($("#'.$id.'").get(0).getContext("2d")).Line('.$id.'_data'.','.$id.'_opts);
';
  $html="";
  $p->JQ($js);
  $p->HTML("<canvas id='$id' class='$id' width='$w' height='$h'></canvas>");
  return $id;
 }

 global $_cpies; $_cpies=0;
 function Pie( &$p, $w=400, $h=400, $datasets, $options=NULL ) {
  global $_chartsjs_included; if ( $_chartsjs_included === FALSE ) { $_chartsjs_included=TRUE; $p->JS('Chart.min.js'); }
  $datasets=json_encode($datasets);
  global $_cpies; $_cpies++;
  global $_chartsjs; $_chartsjs++;
  $id="PieChart".$_cpies;
  $js="";
  $js.='var '.$id.'_data='.($datasets).';
';
  $js.='var '.$id.'_opts={ '.$options.' };
';
  $js.='var '.$id.'=new Chart($("#'.$id.'").get(0).getContext("2d")).Pie('.$id.'_data'.','.$id.'_opts);
';
  $p->JQ($js);
  $p->HTML("<canvas id='$id' class='$id' width='$w' height='$h'></canvas>");
  return $id;
 }

 global $_cdonuts; $_cdonuts=0;
 function Donut( &$p, $w=400, $h=400, $labels, $datasets, $options=NULL ) {
  global $_chartsjs_included; if ( $_chartsjs_included === FALSE ) { $_chartsjs_included=TRUE; $p->JS('Chart.min.js'); }
  $labels=json_encode($labels);
  $datasets=json_encode($datasets);
  if ( false_or_null($options) ) $options=json_encode(array());
  else $options=json_encode($options);
  global $_cdonuts; $_cdonuts++;
  global $_chartsjs; $_chartsjs++;
  $id="DonutChart".$_cdonuts;
  $js="";
  $js.='var '.$id.'_data={ labels:'.json_encode($labels).', datasets:'.json_encode($datasets).' };
';
  $js.='var '.$id.'_opts={ '.$options.' };
';
  $js.='var '.$id.'=new Chart($("#'.$id.'").get(0).getContext("2d")).Doughnut('.$id.'_data'.','.$id.'_opts);
';
  $p->JQ($js);
  $p->HTML("<canvas id='$id' width='$w' height='$h'></canvas>");
  return $id;
 }

 global $_cbars; $_cbars=0;
 function Bar( &$p, $w=400, $h=400, $labels, $datasets, $options=NULL ) {
  global $_chartsjs_included; if ( $_chartsjs_included === FALSE ) { $_chartsjs_included=TRUE; $p->JS('Chart.min.js'); }
  if ( false_or_null($options) ) $options=array();
  global $_cbars; $_cbars++;
  global $_chartsjs; $_chartsjs++;
  $id="BarChart".$_cbars;
  $js="";
  $js.='var '.$id.'_data={ labels:'.json_encode($labels).', datasets:'.json_encode($datasets).' };
';
if ( count($options) > 0 ) {
  $js.='var '.$id.'_opts={ '.json_encode($options).' };
';
} else {
  $js.='var '.$id.'_opts=null;
';
}
  $js.='var '.$id.'=new Chart($("#'.$id.'").get(0).getContext("2d")).Bar('.$id.'_data'.','.$id.'_opts);
';
  $p->JQ($js);
  $p->HTML("<canvas id='$id' width='$w' height='$h'></canvas>");
  return $id;
 }

 global $_cpolars; $_cpolars=0;
 function Polar( &$p, $w=400, $h=400, $labels, $datasets, $options=NULL ) {
  global $_chartsjs_included; if ( $_chartsjs_included === FALSE ) { $_chartsjs_included=TRUE; $p->JS('Chart.min.js'); }
  $labels=json_encode($labels);
  $datasets=json_encode($datasets);
  if ( false_or_null($options) ) $options=json_encode(array());
  else $options=json_encode($options);
  global $_cpolars; $_cpolars++;
  global $_chartsjs; $_chartsjs++;
  $id="PolarChart".$_cpolars;
  $js="";
  $js.='var '.$id.'_data={ labels:'.json_encode($labels).', datasets:'.json_encode($datasets).' };
';
  $js.='var '.$id.'_opts={ '.$options.' };
';
  $js.="var $id=new Chart(".'$("#'.$id.'").get(0).getContext("2d")).PolarArea('.$id.'_data'.','.$id.'_opts);
';
  $p->JQ($js);
  $p->HTML("<canvas id='$id' width='$w' height='$h'></canvas>");
  return $id;
 }

 global $_cradars; $_cradars=0;
 function Radar( &$p, $w=400, $h=400, $labels, $datasets, $options=NULL ) {
  global $_chartsjs_included; if ( $_chartsjs_included === FALSE ) { $_chartsjs_included=TRUE; $p->JS('Chart.min.js'); }
  $labels=json_encode($labels);
  $datasets=json_encode($datasets);
  if ( false_or_null($options) ) $options=json_encode(array());
  else $options=json_encode($options);
  global $_cradars; $_cradars++;
  global $_chartsjs; $_chartsjs++;
  $id="RadarChart".$_cradars;
  $js="";
  $js.='var '.$id.'_data={ labels:'.json_encode($labels).', datasets:'.json_encode($datasets).' };
';
  $js.='var '.$id.'_opts={ '.$options.' };
';
  $js.="var $id=new Chart(".'$("#'.$id.'").get(0).getContext("2d")).Radar('.$id.'_data'.','.$id.'_opts);
';
  $p->JQ($js);
  $p->HTML("<canvas id='$id' width='$w' height='$h'></canvas>");
  return $id;
 }

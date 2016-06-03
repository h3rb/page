<?php

 define('SEP',",");
 define('EOR',"\n");

 global $plog_level; $plog_level=1;
 include 'core/Page.php';

 if ( !Session::logged_in() ) Page::Redirect('login');

 $getpost=getpost();

 $report=intval($getpost['Report']);
 $low=$getpost['Low'];
 $high=$getpost['High'];
 $one_day=false;

 if ( strtotime($low) > strtotime($high) ) {
  if ( strlen($high) > 0 ) {
   $flip=$low;
   $low=high;
   $high=$flip;
  } else {
   if ( strlen($low) > 0 ) {
    $one_day=true;
   } else {
    echo 'Unable to generate a report without a valid date.'; var_dump($getpost); die;
   }
  }
 }

 if ( strlen($low) == 0 ) {
  echo 'Unable to generate a report without a start date.'; var_dump($getpost); die;
 }

 $output='';
 $report_name=CSVReportType::name($report);

 global $database;


 echo $csv;
 $csv=fromcsv($csv);
 echo '<table><tr>';
 foreach ( $csv as $row ) {
  foreach ( $row as $column ) {
   echo '<td>'.$column.'</td>';
  }
 }
 echo '</tr></table>';

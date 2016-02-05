<?php


abstract class CSVReportType extends Enum {
 const Emails=1;
 static function name($n) {
  switch ($n) {
   case CSVReportType::Emails: return 'Emails'; break;
   default: return 'Unknown'; break;
  }
 }
};

abstract class UnitsType extends Enum {
 const Percentage=1;
 const NormalizedRatio=2;
 const Tally=3;
 const USD=4;
 const Seconds=5;
 static function name($n) {
  switch(intval($n)) {
   case UnitsType::Percentage:       return 'Percentage';
   case UnitsType::NormalizedRatio:  return 'Normalized Ratio';
   case UnitsType::Tally:            return 'Tally';
   case UnitsType::USD:              return 'USD';
   case UnitsType::Seconds:          return 'Seconds';
   default: return 'Non-scalar'; break;
  }
 }
};


abstract class TimeframeType extends Enum {
 const Continuous=1;
 const Daily=2;
 const Weekly=3;
 const Monthly=4;
 const Quarterly=5;
 const Yearly=6;
 static function name($n) {
  switch(intval($n)) {
   case TimeframeType::Continuous: return 'Continuous';
   case TimeframeType::Daily: return 'Daily';
   case TimeframeType::Weekly: return 'Weekly';
   case TimeframeType::Monthly: return 'Monthly';
   case TimeframeType::Quarterly: return 'Quarterly';
   case TimeframeType::Yearly: return 'Yearly';
   default: return 'Timeless'; break;
  }
 }
};

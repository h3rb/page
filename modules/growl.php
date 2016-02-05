<?php

 function jGrowl($message) {
  return '(function($){ $(function(){ $.jGrowl("'.$message.'"); }); })(jQuery);';
 }

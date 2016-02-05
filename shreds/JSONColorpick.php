<?php

  function JSONColorpickRGB( &$p, $id, $table, $field, $element, $value, $allowempty=FALSE ) {
   global $_color_rgbs;
   $_color_rgbs++;
   $dom='crgb_'.$_color_rgbs;
   $p->HTML('<input id="'.$dom.'" type="text" value="'.$value.'"/>');
   $p->JQ('
    $("#'.$dom.'").spectrum({
          theme:"sp-dark",
     cancelText:"",
     chooseText:"Done",
    preferredFormat: "rgb",
      showInput:true,
    showPalette:true,
    palette: [
        ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
        ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
        ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
        ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
        ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
        ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
        ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
        ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
    ],
    togglePaletteMoreText: "&raquo;",
    togglePaletteLessText: "&laquo;",
     allowEmpty:'.($allowempty === TRUE ? 'true' : 'false').'
    }).on("change move.spectrum change.spectrum click",function(e,color){
//     console.log(color);
     var evalue=color==null?"":color.toRgbString();
//     console.log(evalue);
     $.ajaxSetup({ cache: false });
     $.ajax({
      cache:false,
      type: "POST",
      dataType: "JSON",
      url:"ajax.bound.json",
      data: { V:evalue, T:"'.$table.'", F:"'.$field.'", I:'.$id.', E:"'.$element.'" }
     }).done(function(e){ });
    });
   ');
  }

  function JSONColorpickRGBA( &$p, $id, $table, $field, $element, $value, $allowempty=FALSE ) {
   global $_color_rgbs;
   $_color_rgbs++;
   $dom='crgb_'.$_color_rgbs;
   $p->HTML('<input id="'.$dom.'" type="text" value="'.$value.'"/>');
   $p->JQ('
    $("#'.$dom.'").spectrum({
     showAlpha:true,
     theme:"sp-dark",
     cancelText:"",
     chooseText:"Done",
    preferredFormat: "rgb",
      showInput:true,
    showPalette:true,
    palette: [
        ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
        ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
        ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
        ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
        ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
        ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
        ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
        ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
    ],
    togglePaletteMoreText: "&raquo;",
    togglePaletteLessText: "&laquo;",
     allowEmpty:'.($allowempty === TRUE ? 'true' : 'false').'
    }).on("change move.spectrum change.spectrum click",function(e,color){
//     console.log(color);
     var evalue=color==null?"":color.toRgbString();
//     console.log(evalue);
     $.ajaxSetup({ cache: false });
     $.ajax({
      cache:false,
      type: "POST",
      dataType: "JSON",
      url:"ajax.bound.json",
      data: { V:evalue, T:"'.$table.'", F:"'.$field.'", I:'.$id.', E:"'.$element.'" }
     }).done(function(e){ });
    });
   ');
  }

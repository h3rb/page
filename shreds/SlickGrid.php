<?php

 /*
    Wraps SlickGrid.js, currently supports only one per page

    Somewhat based on:
    https://github.com/6pac/SlickGrid/blob/master/examples/example3-editing.html
    https://github.com/6pac/SlickGrid/blob/master/examples/example4-model.html
    https://github.com/6pac/SlickGrid/blob/master/examples/example-composite-editor-item-details.html
    https://github.com/6pac/SlickGrid/blob/master/examples/example7-events.html

    Implements:
    SlickGrid(
     &$p,
     $columns,
     $table,
     $field,
     $id,
     $value,
     $height="500px",
     $save_as_json=FALSE,
     $csv_char='|'
    )

    if $save_as_json is FALSE, information is saved as a CSV string

    format of $columns:

     array(
      array(
       "id"=>"json_field_name", (optional, generated from name if not set)
       "field"=>"json_field_name", (optional, generated from name if not set)
       "name"=>"Any Old String",   (column name)
       "type"=>SlickType(enum),
       "value"=>incoming,
       "options"=>special settings json for slick columns, (like minWidth, width
       "validator"=>slick-style validator javascript function name, (optional)
       ["values"=>array() of selectables] (required for SlickType::Selection type)
      ),
            .
          .
        .
     )
   must correspond to value_json, json will be written to the database on change

   Some column options examples:
    {id: "title", name: "Title", field: "title", width: 120, cssClass: "cell-title", editor: Slick.Editors.Text, validator: requiredFieldValidator},
    {id: "desc", name: "Description", field: "description", width: 100, editor: Slick.Editors.LongText},
    {id: "duration", name: "Duration", field: "duration", editor: Slick.Editors.Text},
    {id: "%", name: "% Complete", field: "percentComplete", width: 80, resizable: false, formatter: Slick.Formatters.PercentCompleteBar, editor: Slick.Editors.PercentComplete},
    {id: "start", name: "Start", field: "start", minWidth: 60, editor: Slick.Editors.Date},
    {id: "finish", name: "Finish", field: "finish", minWidth: 60, editor: Slick.Editors.Date},
    {id: "effort-driven", name: "Effort Driven", width: 80, minWidth: 20, maxWidth: 80, cssClass: "cell-effort-driven", field: "effortDriven", formatter: Slick.Formatters.Checkmark, editor: Slick.Editors.Checkbox}

 */

 global $_slick_id;

 class SlickType extends Enum {
  const Text=1;
  const Decimal=2;
  const Integer=3;
  const Date=4;
  const YesNoSelect=5;
  const Checkbox=6;
  const Percent=7;
  const LongText=8;
  const Selection=9;
  const EnumSelect=10; // not in slick.editors
 static function name($n) {
  switch(intval($n)) {
   case SlickType::Text: return 'Text';
   case SlickType::Decimal: return 'Decimal';
   case SlickType::Integer: return 'Integer';
   case SlickType::Date: return 'Date';
   case SlickType::YesNoSelect: return 'Yes/No';
   case SlickType::Checkbox: return 'Checkbox';
   case SlickType::Percent: return 'Percent';
   case SlickType::LongText: return 'Long Text';
   case SlickType::Selection: return 'Selection';
   case SlickType::EnumSelect: return 'Enumerated';
   default: return 'Unknown'; break;
  }
 }
 static function slickname($n) {
  switch(intval($n)) {
   case SlickType::Text: return 'Text';
   case SlickType::Decimal: return 'Float';
   case SlickType::Integer: return 'Integer';
   case SlickType::Date: return 'Date';
   case SlickType::YesNoSelect: return 'YesNoSelect';
   case SlickType::Checkbox: return 'Checkbox';
   case SlickType::Percent: return 'PercentComplete';
   case SlickType::LongText: return 'LongText';
   case SlickType::Selection: return 'Selection';
   case SlickType::EnumSelect: return 'N/A';
   default: return 'Unknown'; break;
  }
 }
 };

 function EnableSlickGrid( &$p ) {
  $p->JS("lib/firebugx.js");
  $p->JQuery();
  $p->JS("lib/jquery.event.drag-2.2.js");
  $p->JS("slick.core.js");
  $p->JS("plugins/slick.cellrangedecorator.js");
  $p->JS("plugins/slick.cellrangeselector.js");
  $p->JS("plugins/slick.cellselectionmodel.js");
  $p->JS("slick.formatters.js");
  $p->JS("slick.editors.js");
  $p->JS("slick.grid.js");
 }

 function SlickGrid( &$p, $columns, $table, $field, $id, $value, $height="500px", $save_as_json=FALSE, $csv_char='|' ) {
  global $slick_id;
  $slick_id++;
  $dom='slick'.$slick_id;
  foreach ( $columns as &$c ) {
   if ( !isset($c['id']) ) $c['id']=str_replace('-','_',slugify($c['name']));
   if ( !isset($c['field']) ) $c['field']=str_replace('-','_',slugify($c['name']));
  } unset($c);
  if ( $save_as_json !== FALSE ) {
   $encoded_values=$value;
   if ( strlen($encoded_values) < 2 ) $encoded_values='[]';
   $p->JS('function save_'.$dom.'() {
    console.log("Saving JSON");
    console.log(grid_'.$dom.'.getData());
   }');
  } else {
   $csv=explode("\n",$value);
   foreach ( $csv as &$row ) $row=explode("|",$row);
   $encoded_values=array();
   $encoded=array();
   foreach ( $csv as $_r ) {
    $i=0;
    foreach ( $columns as $_c ) {
     $encoded[$_c['id']]=$_r[$i];
     $i++;
    }
    $encoded_values[]=$encoded;
   }
   $encoded_values=json_encode($encoded_values);
   $p->JS('function save_'.$dom.'() {
    console.log("Saving CSV");
    console.log(grid_'.$dom.'.getData());
   }');
  }
  $p->HTML('
   <div class="formhighlight">
    <div class="wide"><div id="'.$dom.'" style="width:100%; height:'.$height.';"></div></div>
    <button onclick="undo_'.$dom.'()"><img src="i/slick/arrow_undo.png" align="absmiddle"> Undo</button>
   </div>');
  $p->JS('
  var grid_'.$dom.';
  var commandQueue_'.$dom.' = [];
  function queueAndExecuteCommand_'.$dom.'(item, column, editCommand) {
    commandQueue_'.$dom.'.push(editCommand);
    editCommand.execute();
  }
  function undo_'.$dom.'() {
    var command = commandQueue_'.$dom.'.pop();
    if (command && Slick.GlobalEditorLock.cancelCurrentEdit()) {
      command.undo();
      grid.gotoCell(command.row, command.cell, false);
    }
  }
');
  $columns_json=array();
  foreach ( $columns as $c ) {
   $column_json='';
   $column_json.='name: "'.($c['name']).'",';
   if ( isset($c['id']) ) $column_json.='id: "'.($c['id']).'",';
   else $column_json.='id: "'.slugify($c['name']).'",';
   if ( isset($c['field']) ) $column_json.='field: "'.($c['field']).'",';
   else $column_json.='field: "'.slugify($c['name']).'",';
   $column_json.='editor: Slick.Editors.'.SlickType::slickname($c['type']).',';
   if ( isset($c['validator']) ) $column_json.='validator: '.$c['validator'].',';
   if ( isset($c['values']) ) $column_json.='values: '.json_encode($c['values']).',';
   if ( isset($c['options']) ) foreach ( $c['options'] as $named=>$opt ) $column_json.=$named.':"'.$opt.'",';
   $column_json=rtrim($column_json,", ");
   $columns_json[]='{'.$column_json.'}';
  }
  $columns_json=implode(",\n",$columns_json);
  $p->JQ('
{
  var data_'.$dom.' = [];
  var columns_'.$dom.' = [
'.$columns_json.'
];
  var options_'.$dom.' = {
    editable: true,
    enableAddRow: true,
    enableCellNavigation: true,
    asyncEditorLoading: false,
    autoEdit: false,
//  forceFitColumns: false,
    topPanelHeight: 25,
    rowHeight: 30,
    editCommandHandler: queueAndExecuteCommand_'.$dom.'
  };
  $(function () {
    var data_'.$dom.'='.($encoded_values).';
    grid_'.$dom.' = new Slick.Grid("#'.$dom.'", data_'.$dom.', columns_'.$dom.', options_'.$dom.');
    grid_'.$dom.'.setSelectionModel(new Slick.CellSelectionModel());
    grid_'.$dom.'.onAddNewRow.subscribe(function (e, args) {
      var item = args.item;
      grid_'.$dom.'.invalidateRow(data_'.$dom.'.length);
      data_'.$dom.'.push(item);
      grid_'.$dom.'.updateRowCount();
      grid_'.$dom.'.render();
      save_'.$dom.'();
    });
    grid_'.$dom.'.onCellChange.subscribe(function (r,c,i) {
     save_'.$dom.'();
    });
  })
}
');
  return $dom;
 }

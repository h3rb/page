<?php

 include_once 'Coord.php';
 include_once 'Crayon.php';
 include_once 'CSS.php';

 // Treats a table as a grid and uses classes to manage the CSS for each grid cell.

 class GridCell {
  var $x,$y,$tag,$value,$bg,$fg,$css,$mergeRight;
  public function __construct( $x, $y ) {
   $this->x=$x;
   $this->y=$y;
   $this->tag=array();
   $this->value='';
   $this->bg=new Crayon; $this->bg->SetRGB(1,1,1);
   $this->fg=new Crayon; $this->fg->SetRGB(0,0,0);
   $this->css=new CSS;
   $this->mergeRight=0;
  }
  public function Reset() {
   $this->tag=array();
   $this->value='';
   $this->bg=new Crayon;  $this->bg->SetRGB(1,1,1);
   $this->fg=new Crayon;  $this->fg->SetRGB(0,0,0);
   $this->css=new CSS;
   $this->mergeRight=0;
  }
  public function Back( $r, $g, $b ) { $this->bg->SetRGB($r,$g,$b); }
  public function Fore( $r, $g, $b ) { $this->fg->SetRGB($r,$g,$b); }
  public function Duplicate($x,$y) {
   $cell=new GridCell($x,$y);
   $cell->tag=array_copy($this->tag);
   $cell->value=$this->value;
   $cell->fg=$this->fg->Duplicate();
   $cell->bg=$this->bg->Duplicate();
   $cell->css=$this->css->Duplicate();
   $cell->mergeRight=$this->mergeRight;

   return $cell;
  }
  public function GetTag($tagname,$tagarray=NULL,$interior=FALSE,$closetag=FALSE) {
   if ( !is_null($this->fg) ) $this->css->Style('color',$this->fg->RGBA());
   if ( !is_null($this->bg) ) $this->css->Style('background-color',$this->bg->RGBA());
   return $this->css->GetTag($tagname,$tagarray,$interior,$closetag);
  }
 };

 class GridPen {
  var $bg,$fg;
  var $css;
  var $mergeRight;
  var $operation;
  var $blend,$alpha; // valid blend modes are + - * = add sub mult overwrite
  var $value;
   // "Clear Values"
   // "Set Background only"
   // "Set Foreground only"
   // "Set Both"
  public function __construct() {
   $this->Reset();
  }
  public function Reset() {
   $this->bg=new Crayon( 1,1,1 );
   $this->fg=new Crayon( 0,0,0 );
   $this->css=NULL;
   $this->mergeRight=0;
   $this->operation=3;
   $this->blend='=';
   $this->value=NULL;
   $this->alpha=1.0;  // used to lerp / mix
  }
  public function Back( $r, $g, $b ) { $this->bg->SetRGB($r,$g,$b); }
  public function Fore( $r, $g, $b ) { $this->fg->SetRGB($r,$g,$b); }
  public function Ink($inCell) {
   $cell=$inCell->Duplicate($inCell->x,$inCell->y);
 //  echo 'in: '; var_dump($inCell);
 //  echo 'out:'; var_dump($cell);
   $this->Frame($cell);
   $this->Operate($cell);
   return $cell;
  }
  public function Operate(&$cell) {
   if ( !is_null($this->value) ) $cell->value=$this->value;
   if ( $this->mergeRight > 0 ) $cell->mergeRight=$this->mergeRight;
   if ( !is_null($this->css) ) $cell->css=$this->css->Duplicate();
   switch ( $this->operation ) {
    case 0: // Do not affect color
    break;
    case 1: // Set Foreground
     $cell->fg->Blend($this->blend,$this->fg);
    break;
    case 2: // Set Background
     $cell->bg->Blend($this->blend,$this->bg); //var_dump($cell->bg); var_dump($this->bg);
    break;
    case 3: // Set Both
     $cell->fg->Blend($this->blend,$this->fg);
     $cell->bg->Blend($this->blend,$this->bg);
    break;
   }
   if ( !is_null($cell->fg) ) $cell->css->Style('color',$cell->fg->RGBA());
   if ( !is_null($cell->bg) ) $cell->css->Style('background-color',$cell->bg->RGBA());
  }
  public function fromCell( $cell ) {
   $this->bg=$cell->bg->Duplicate();
   $this->fg=$cell->fg->Duplicate();
   $this->value=$cell->value;
   $this->mergeRight=$cell->mergeRight;
   $this->css=$cell->css->Duplicate();
  }
  // Modified in the child, this is executed once per draw cycle
  public function Frame($cell) {}
 };

///////////////////////////////////////////////////////////////////////

 class GridHelper {
  var $grid,$w,$h,$table,$thead,$th,$tbody,$tr,$td,$tfoot,$tf,$headings,$footer;
  public function __construct() {
   $this->grid=array( 0=>array( 0=>new GridCell(0,0) ) );
   $this->w=0;
   $this->h=0;
   $this->table=new CSS;
   $this->thead=new CSS;
   $this->thead->Style( "font-weight", "bold" );
   $this->tbody=new CSS;
   $this->tr=new CSS;
   $this->td=new CSS;
   $this->tfoot=new CSS;
   $this->tfoot->Style( "text-decoration", "italic" );
   $this->tf=new CSS;
   $this->headings=array( 0=>new GridCell(0,0) );
   $this->headings[0]->css=$this->thead->Duplicate();
   $this->footer=array( 0=>new GridCell(0,0) );
   $this->footer[0]->css=$this->tfoot->Duplicate();
   $args=func_get_args();
   if ( count($args) == 2 ) $this->Size($args[0],$args[1]);
  }

  // Checks boundaries for x,y
  public function within( $x, $y ) {
   return ( $x >= 0 && $x < $this->w && $y >= 0 && $y < $this->h ) ? TRUE : FALSE;
  }
  public function negative($x,$y) { return ( $x < 0 || $y < 0 ) ? TRUE : FALSE; }

  public function Get( $x, $y ) {
   if ( $this->negative($x,$y) || !isset($this->grid[$x][$y]) ) return FALSE;
   return $this->grid[$x][$y];
  }
  public function Set( &$pen, $x, $y, $expand_to_fit=TRUE ) {
   if ( $this->negative($x,$y) ) return FALSE;
   if ( $expand_to_fit === TRUE ) {
    if ( !$this->within($x,$y) ) $this->Size($x,$y);
    $this->grid[$x][$y]=$pen->Ink($this->grid[$x][$y]);
    return TRUE;
   }
   if ( $this->within($x,$y) ) { $this->grid[$x][$y]=$pen->Ink($this->grid[$x][$y]); return TRUE; }
   return FALSE;
  }

  public function DebugString($html=TRUE) {
   $out='';
   if ( $html === TRUE ) {
   } else { // plain text
   }
   return $out;
  }

  // Copies values from one part of the grid to another.
  public function Copy( $x, $y, $w, $h, $x2, $y2, $expand_to_fit=TRUE ) {
   if ( $this->negative($x,$y) ) return FALSE;
   if ( $expand_to_fit === TRUE ) {
    $cols=0;
    $rows=0;
    if ( $x2+$w >= $this->w ) $cols=($x2+$w)-$this->w;
    if ( $y2+$h >= $this->h ) $rows=($y2+$h)-$this->h;
    while ( $cols > 0 ) { $cols--; $this->AppendColumn(); }
    while ( $rows > 0 ) { $rows--; $this->AppendRow();    }
   }
   $array=array();
   for ( $i=0; $i< $this->w; $i++ ) for ( $j=0; $j< $this->h; $j++ ) {
    $array[$i][$j]=$this->grid[$i][$j]->Duplicate($i,$j);
   }
   if ( $expand_to_fit === TRUE ) {
    for ( $i=0; $i< $w; $i++ ) for ( $j=0; $j<$h; $j++ ) {
     $array[$x2+$i][$y2+$j]=$this->grid[$x+$i][$y+$j]->Duplicate($x+$i,$y+$j);
    }
   } else {
    for ( $i=0; $i< $w; $i++ ) for ( $j=0; $j<$h; $j++ ) {
     $a=$x2+i; $b=$y2+$j; $c=$x+$i; $d=$y+$j;
     if ( $this->within($a,$b) && $this->within($b,$c) )
      $array[$a][$b]=$this->grid[$c][$d]->Duplicate($a,$b);
    }
   }
   $this->grid=$array;
   return TRUE;
  }

  // Copies the values and styles of a to b
  public function CopyColumn( $a, $b, $expand_to_fit=TRUE ) {
   if ( !$this->within($a,0) ) return FALSE;
   while ( $b >= $this->w ) $this->AppendColumn();
   for ( $i=0; $i < $this->h; $i++ ) $this->grid[$b][$i]=$this->grid[$a][$i]->Duplicate($b,$i);
  }

  // Copies the styles from a previously styled row, and writes values provided.
  // Third parametere is a target row or if TRUE, appends to the table.
  public function Row( $style_row, $row_values, $append=TRUE ) {
   $styles=array();
   if ( $append === TRUE ) {
    $this->AppendRow();
    $target_row=$this->h-1;
   } else $target_row=$append;
   for ( $i=0; $i < $this->w; $i++ ) {
    $this->grid[$i][$target_row]=$this->grid[$i][$style_row]->Duplicate($i,$target_row);
    if ( isset($row_values[$i]) ) $this->grid[$i][$target_row]->Value($row_values[$i]);
   }
  }

  // Appends a new blank column copying style from this->td.
  public function AppendColumn() {
   $this->grid[$this->w]=array();
   for ( $i=0; $i< $this->h; $i++ ) {
    $this->grid[$this->w][$i]=new GridCell($this->w,$i);
    $this->grid[$this->w][$i]->css=$this->td->Duplicate();
   }
   $this->headings[$this->w]=new GridCell($this->w,0);
   $this->footer[$this->w]=new GridCell($this->w,0);
   $this->w++;
  }
  // Appends a new blank row copying style from this->td.
  public function AppendRow() {
   for ( $i=0; $i< $this->w; $i++ ) {
    $this->grid[$i][$this->h]=new GridCell($i,$this->h);
    $this->grid[$i][$this->h]->css=$this->td->Duplicate();
   }
   $this->h++;
  }
  // Truncates by 1 column.
  public function RemoveLastColumn() {
   for ( $i=0; $i< $this->h; $i++ ) unset($this->grid[$this->w][$i]);
   unset($this->headings[$this->w]);
   unset($this->footer[$this->w]);
   $this->w--;
  }
  // Truncates by 1 row.
  public function RemoveLastRow() {
   for ( $i=0; $i< $this->w; $i++ ) unset($this->grid[$i][$this->h]);
   $this->h--;
  }

  // Truncates or expands the width of the grid.
  public function ResizeWidth( $w ) {
   if ( $w < 0 ) return FALSE;
   $addCol=0;
   $removeCol=0;
   if ( $w > $this->w ) {
    $addCol=($w-$this->w);
   } else if ( $w < $this->w-1 ) {
    $removeCol=$this->w-$w;
   } else return 0;
   for ( $i=0; $i<$addCol; $i++ ) $this->AppendColumn();
   for ( $i=0; $i<$removeCol; $i++ ) $this->RemoveLastColumn();
   return TRUE;
  }

  // Truncates or expands the height of the grid.
  public function ResizeHeight( $h ) {
   if ( $h < 0 ) return FALSE;
   $addRow=0;
   $removeRow=0;
   if ( $h > $this->h ) {
    $addRow=($h-$this->h);
   } else if ( $h < $this->h-1 ) {
    $removeRow=$this->h-$h;
   } else return 0;
   for ( $i=0; $i<$addRow; $i++ ) $this->AppendRow();
   for ( $i=0; $i<$removeRow; $i++ ) $this->RemoveLastRow();
   return TRUE;
  }

  // Changes the size of the grid, truncating or appending.
  public function Size( $w, $h ) {
   if ( $this->negative($w,$h) ) return FALSE;
   $this->ResizeWidth( $w );
   $this->ResizeHeight( $h );
   return TRUE;
  }

  // Adjusts size to accomodate x,y if x,y is not on the current grid.
  public function Fit( $x, $y ) {
   if ( $this->negative($x,$y) ) return FALSE;
   if ( $x >= $this->w ) $this->ResizeWidth($x);
   if ( $y >= $this->h ) $this->ResizeHeight($y);
   return TRUE;
  }

  // Sets the value for a grid position
  public function Value( $x, $y, $v, $expand_to_fit=TRUE ) {
   if ( $this->negative($x,$y) ) return FALSE;
   if ( $expand_to_fit === TRUE ) {
    $this->Fit($x,$y);
    $this->grid[$x][$y]->value=$v;
   } else {
    if ( $this->within($x,$y) ) {
     $this->grid[$x][$y]->value=$v;
    }
   }
   return TRUE;
  }

  // Sets all of the values across an entire column
  public function SetColumn(&$pen, $x, $starting=0, $direction=1 ) {
   for ( $i=$starting; $i<$this->h && $i>=0; $i+=$direction ) $this->grid[$x][$i]=$pen->Ink($this->grid[$x][$i]);
  }

  // Sets all of the values across an entire row
  public function SetRow(&$pen, $y, $starting=0, $direction=1 ) {
   for ( $i=$starting; $i<$this->w && $i>=0; $i+=$direction ) $this->grid[$i][$y]=$pen->Ink($this->grid[$i][$y]);
  }

  // integer $direction is like the numpad (2=down, 8=up, 9=up+right, etc)
  // integer $length is the # of rows/columns to affect, where -1 is 'until the edge is hit'
  public function Line( &$pen, $x, $y, $direction, $length=-1 ) {
   if ( $length === 0 ) return FALSE;
   $dx=0;
   $dy=0;
   switch ( $direction ) {
    case 1: $dx=-1; $dy=-1; break;
    case 2: $dx=0;  $dy=-1; break;
    case 3: $dx=1;  $dy=-1; break;
    case 4: $dx=-1; $dy=0; break;
    case 6: $dx=1;  $dy=0; break;
    case 7: $dx=-1; $dy=1; break;
    case 8: $dx=0;  $dy=1; break;
    case 9: $dx=1;  $dy=1; break;
    default: return FALSE;
   }
   $iteration=0;
   $px=$x;
   $py=$y;
   if ( $length > 0 ) {
    while ( $iteration < $length ) {
     $iteration++;
     $px+=$dx;
     $py+=$dy;
    }
   }
   return TRUE;
  }

  public function HLine( &$pen, $x, $y, $x2=-1 ) {
   if ( $x2 === -1 ) $x2=$this->w-1;
   for ( $i=$x; $i<=$x2; $i++ ) $this->Set($pen,$i,$y);
  }

  public function VLine( &$pen, $x, $y, $y2=-1 ) {
   if ( $y2 === -1 ) $y2=$this->h-1;
   for ( $i=$y; $i<=$y2; $i++ ) $this->Set($pen,$x,$i);
  }

  public function Rectangle( &$pen, $x, $y, $w, $h ) {
   $x2=$x+$w;
   for ( $i=$x; $i<$x2; $i++ ) {
    $this->VLine($pen,$i,$y,$y+$h);
   }
  }

  public function EvenOddRow( &$pen_odd, &$pen_even, $starting_row=0, $ending_row=-1 ) {
   if ( $ending_row === -1 ) $ending_row=$this->h-1;
   for ( $i=$starting_row; $i <= $ending_row; $i++ ) {
    if ( $i % 2 == 0 )
    $this->HLine( $pen_even, 0, $i );
    else
    $this->HLine( $pen_odd,  0, $i );
   }
  }

  public function EvenOddColumn( &$pen_odd, &$pen_even, $starting_column=0 ) {
   for ( $i=0; $i+$starting_column < $this->w; $i+=2 ) $this->VLine( $pen_even, $starting_column+$i, 0 );
   for ( $i=1; $i+$starting_column < $this->w; $i+=2 ) $this->VLine( $pen_odd, $starting_column+$i, 0 );
  }

  public function Circle( &$pen, $x, $y, $R, $filled=TRUE, $expand_to_fit=TRUE ) {
   if ( $expand_to_fit === TRUE && !$this->within($x+$R,$y+$R) ) $this->Fit($x+$R,$y+$R);
   if ( $filled === TRUE ) {
    $px = $R;
    $py = 0;
    $Re=1-$x;
    while( $px >= $py )  {
     $this->HLine($pen, -$px + $x,  $py + $y, $px + $x);
     $this->HLine($pen, -$py + $x,  $px + $y, $py + $x);
     $this->HLine($pen, -$px + $x, -$py + $y, $px + $x);
     $this->HLine($pen, -$py + $x, -$px + $y, $py + $x);
     $py++;
     if ($Re < 0) $Re += 2 * $py + 1;
     else {
      $px--;
      $Re += 2 * ($py - $px) + 1;
     }
    }
   } else {
    $px = $R;
    $py = 0;
    $Re=1-$x;
    while( $px >= $py )  {
     $this->Set($pen,  $px + $x,  $py + $y);
     $this->Set($pen,  $py + $x,  $px + $y);
     $this->Set($pen, -$px + $x,  $py + $y);
     $this->Set($pen, -$py + $x,  $px + $y);
     $this->Set($pen, -$px + $x, -$py + $y);
     $this->Set($pen, -$py + $x, -$px + $y);
     $this->Set($pen,  $px + $x, -$py + $y);
     $this->Set($pen,  $py + $x, -$px + $y);
     $py++;
     if ($Re < 0) $Re += 2 * $py + 1;
     else {
      $px--;
      $Re += 2 * ($py - $px) + 1;
     }
    }
   }
  }

  public function Attenuate( &$pen, $x, $y, $R ) { /* not yet implemented */ }

  // Sets just the value of one column heading
  public function Heading( $x, $v )  {
   if ( $x >= $this->w ) $this->ResizeWidth($x+1);
   if ( !isset($this->headings[$x]) ) $this->headings[$x]=new GridCell($x,0);
   $this->headings[$x]->value=$v;
  }

  // Sets just the value of one column footer
  public function Footer( $x, $v ) {
   if ( $x >= $this->w ) $this->ResizeWidth($x+1);
   if ( !isset($this->footer[$x]) ) $this->footer[$x]=new GridCell($x,0);
   $this->footer[$x]->value=$v;
  }

  public function Render() {
   $html=$this->table->GetTag('table').PHP_EOL;
   if ( !is_null($this->thead) && count($this->headings) > 0 ) {
    $html.=' '.$this->thead->GetTag('thead').PHP_EOL;
    for ( $i=0; $i < $this->w; $i++ ) {
     if ( isset($this->headings[$i]) ) { //var_dump($this->headings); var_dump($i);
      $html.= '  '.$this->headings[$i]->GetTag('th',$this->headings[$i]->tag,$this->headings[$i]->value,TRUE).PHP_EOL;
     } else {
      $html.= '  '.$this->thead->GetTag('th',array(),'',TRUE).PHP_EOL;
     }
    }
    $html.=' </thead>'.PHP_EOL;
   }
   $html.=' '.$this->tbody->GetTag('tbody').PHP_EOL;
   for ( $j=0; $j < $this->h; $j++ ) {
    $html.='  '.$this->tr->GetTag('tr').PHP_EOL;
    for ( $i=0; $i < $this->w; $i++ ) {
     $tag=array();
     if ( $this->grid[$i][$j]->mergeRight > 0 ) {
      $tag['colspan']=$this->grid[$i][$j]->mergeRight;
      $i+=($this->grid[$i][$j]->mergeRight-1);
     }
     $html.='   '.$this->grid[$i][$j]->GetTag('td',$ta,$this->grid[$i][$j]->value,TRUE).PHP_EOL;
    }
    $html.='  </tr>'.PHP_EOL;
   }
   $html.=' </tbody>'.PHP_EOL;
   if ( !is_null($this->tfoot) && count($this->footer) > 0 ) {
    $html.=' '.$this->tfoot->GetTag('tfoot').PHP_EOL;
    for ( $i=0; $i < $this->w; $i++ ) {
     if ( isset($this->footer[$i]) ) {
      $html.= '  '.$this->footer[$i]->GetTag('tf',$this->footer[$i]->tag,$this->footer[$i]->value,TRUE).PHP_EOL;
     } else {
      $html.= '  '.$this->tfoot->GetTag('tf',array(),'',TRUE).PHP_EOL;
     }
    }
    $html.=' </tfoot>'.PHP_EOL;
   }
   $html.='</table>'.PHP_EOL;
   return $html;
  }

 };

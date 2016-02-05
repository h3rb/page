<?php

 class TBGridElement {
  var $content,$size;
  public function __construct( $content, $size ) {
   $this->content=$content;
   $this->size='span'.$size;
  }
  public function Source() { return '<div class="'.$this->size.'">'.$this->content.'</div>'; }
 };

 class TBGrid {
  var $rows,$row,$row_meta;
  public function __construct( $a ) {
   $this->rows=array();
   $this->row_meta=array();
   $this->row=-1;
  }
  public function Row( $fluid=TRUE ) {
   $this->row++;
   $this->rows[$this->row]=array();
   $this->row_meta[$this->row]=($fluid === TRUE ? "row-fluid" : "row");
  }
  public function Add( $content, $size="4" ) {
   $this->rows[$this->row] = new TBGridElement( $content, $size );
  }
  public function Source() {
   $out='<div class="bs-docs-grid">';
   $rows=count($this->rows);
   for ( $i=0; $i<$rows; $i++ ) {
    $out.='<div class="'.$this->row_meta[$i].'">';
    foreach ( $this->rows[$i] as $r ) {
     $out.=$r->Source();
    }
    $out.='</div>';
   }
   $out.='</div>';
  }
 };

 abstract class TBootstrap {

  public function H1($t) { return TBootstrap::PageHeading($t); }
  public function PageHeading( $title ) {
   return '<h1 class="page-header">'.$title.'</h1>'.PHP_EOL;
  }

  public function H2($t) { return TBootstrap::Heading($t); }
  public function Heading( $title ) {
   return '<h2 class="sub-header">'.$title.'</h2>'.PHP_EOL;
  }

  public function H4($t) { return TBootstrap::Label($t); }
  public function Label( $title ) {
   return '<h4>'.$title.'</h4>';
  }

  public function Grid( $grid, $type="span2" ) {
   $out='<div class="bs-docs-grid">';
   foreach ( $grid as $row ) {
    $out.='<div class="row-fluid show-grid">';
    foreach ( $row as $element ) {
     $out.='<div class="'.$type.'">'.$element.'</div>';
    }
    $out.='</div>';
   }
   $out.='</div>';
  }

  public function TopBar($title,$icons,$menu,$form=FALSE) {
   $out='<nav class="navbar navbar-inverse navbar-fixed-top"><div class="container-fluid">'.PHP_EOL;
   $out.='<div class="navbar-header"><button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">'.PHP_EOL;
   $out.='<span class="sr-only">Toggle navigation</span>'.PHP_EOL;
   $out.='<span class="icon-bar"></span>'.PHP_EOL;
   $out.='<span class="icon-bar"></span>'.PHP_EOL;
   $out.='<span class="icon-bar"></span>'.PHP_EOL;
   if ( is_array($icons) && count($icons) > 0 ) {
    foreach ( $icons as $icon ) {
     $out.='<span class="icon-bar">'.$icon.'</span>'.PHP_EOL;
    }
   }
   $out.='</button>'.PHP_EOL;
   $out.='<a class="navbar-brand" href="#">'.$title.'</a>'.PHP_EOL;
   $out.='</div>'.PHP_EOL;
   $dd=0;
   if ( is_array($menu) && count($menu) > 0 ) {
    $out.='<div id="navbar" class="navbar-collapse collapse">';
    $out.='<ul class="nav navbar-nav navbar-right">'.PHP_EOL;
    foreach ( $menu as $named=>$option ) {
     if ( !is_array($option) ) {
      $out.='<li><a href="'.$named.'">'.$option.'</a></li>';
     } else if ( count($option)==1 ) {
      foreach ( $option as $o=>$u ) {
       $out.='<li><a href="'.$u.'">'.$o.'</a></li>'.PHP_EOL;
      }
     } else {
      $dd++;
      $out.='<li class="dropdown">'.PHP_EOL;
      $out.='<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
      $out.=$named;
      $out.='<span class="caret"></span></a>'.PHP_EOL;
      $out.=
      $out.='<ul class="dropdown-menu" role="menu">'.PHP_EOL;
      foreach ( $option as $order=>$u) {
       if ( is_array($u) ) {
        foreach ( $u as $named=>$url ) {
         $out.='<li><a href="'.$url.'">'.$named.'</a></li>'.PHP_EOL;
        }
       } else if ( matches($u,'divider') || matches($u,'---') || matches($u,'-') ) {
        $out.='<li class="divider"></li>'.PHP_EOL;
       } else {
       $out.='<li class="dropdown-header">'.str_replace("header:","",$u).'</li>'.PHP_EOL;
       }
      }
      $out.='</ul>';
      $out.='</li>';
     }
    }
    $out.='</ul></div>'.PHP_EOL;
   }
   if ( $form !== FALSE ) {
    $out.='<form action="'.$form['action'].'" method="'.$form['method'].'" class="navbar-form navbar-right">';
    foreach ( $form['elements'] as $e ) {
     $out.=$e;
    }
    $out.='</form>'.PHP_EOL;
   }
   $out.='</div></nav>'.PHP_EOL;
   return $out;
  }

  public function LayoutSidebarStart($sidebar) {
   $out='<div class="container-fluid"><div class="row"><div class="col-sm-3 col-md-2 sidebar">';
   foreach ( $sidebar as $elementGroup ) {
    $out.='<ul class="nav nav-sidebar">'.PHP_EOL;
    foreach ( $elementGroup as $named=>$url ) {
     if ( matches($named,"active") ) {
      $out.='<li class="active"><a href="#">'.$url.'<span class="sr-only">(current)</span></a></li>';
     } else {
      $out.='<li><a href="'.$url.'">'.$named.'</a></li>';
     }
    }
    $out.='</ul>'.PHP_EOL;
   }
   $out.='<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">'.PHP_EOL;
   return $out;
  }

  public function LayoutSidebarEnd() {
   return '</div></div></div></div>'.PHP_EOL;
  }

  // Four columns best?
  public function Columns( $columns ) {
   $out='<div class="row placeholders">';
   foreach ( $columns as $item ) {
    $out.='<div class="col-xs-6 col-sm-3 placeholder">'.$item.'</div>';
   }
   $out.='</div>';
   return $out;
  }

  // Other options for classXtras: table-boredered, table-condensed
  public function Table( $headers, $data, $classXtras="table-striped" ) {
   $out='<div class="table-responsive"><table class="table '.$classXtras.'">';
   if ( is_string($headers) ) $headers=words($headers,'|');
   if ( is_array($headers) ) {
    if ( count($headers) > 0 ) {
     $out.='<thead><tr>';
     foreach ( $headers as $title ) {
      $out.='<th>'.$title.'</th>';
     }
     $out.='<tr></thead>';
     $out.=PHP_EOL;
    }
   } else return "";
   $out.='<tbody>'.PHP_EOL;
   foreach ( $data as $row ) {
    $out.='<tr>';
    foreach ( $row as $element ) {
     $out.='<td>';
     $out.=$element;
     $out.='</td>';
    }
    $out.='</tr>'.PHP_EOL;
   }
   $out.='</tbody>'.PHP_EOL;
   $out.='</table></div>'.PHP_EOL;
   return $out;
  }

  // Options for $size: btn-lg (normal) btn-sm btn-xs
  public function Button( $label, $type="default", $size="" ) {
   return '<button type="button" class="btn '.$size.' btn-'.$type.'">'.$label.'</button>';;
  }

  public function LabelBadge( $label, $value, $type="default" ) {
   return TBootstrap::BoxLabel($label.TBootstrap::Badge($value),$type);
  }

  public function BoxLabel( $value, $url="#", $type="default" ) {
   return '<span class="label label-'.$type.'"><a href="'.$url.'">'.$value.'</a></span>';
  }

  public function Badge( $value ) {
   return '<span class="badge">'.$value.'</span>';
  }

  public function Alert( $content, $type="success" ) {
   return '<span class="alert alert-'.$type.'" role="alert">'.$value.'</span>';
  }

  public function Panel( $heading, $interior, $type="default" ) {
   return '<div class="panel panel-'.$type.'">'
     .'<div class="panel-heading"><h3 class="panel-title">'.$heading.'</h3></div>'
     .'<div class="panel-body">'.$interior.'</div>'
     .'</div>'.PHP_EOL;
  }

  public function Well( $lorem ) {
   return '<div class="well">'.$lorem.'</div>';
  }

  public function Carousel( $slides ) {
   $out='<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">';
   for ( $i=0; $i<count($slides); $i++ ) {
    if ( $i==0 ) {
     $out.='
          <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
';
    } else {
     $out.='
          <li data-target="#carousel-example-generic" data-slide-to="'.$i.'"></li>
';
    }
   }
   $out.='
        </ol>
        <div class="carousel-inner" role="listbox">
        ';
   $i=0;
   foreach ( $slides as $slide ) {
    if ( $i == 0 ) {
     $out.='<div class="item active">'.$slide.'</div>';
    } else {
     $out.='<div class="item">'.$slide.'</div>';
    }
    $i++;
   }
   $out.='</div>';
   $out.='
        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
          <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
          <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
</div>
';
   return $out;
  }

 };

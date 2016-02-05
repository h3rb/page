<?php

 include '../core/Page.php';

 $p = new Page;

 $g=new GridHelper;

 $g->table->tag['cellspacing']=0;
 $g->table->tag['cellpadding']=0;
 $g->td->Border( "solid", "black", "1px" );
 $g->td->styling['padding']='5px';
 $g->td->styling['margin']=0;
 $g->table->styling['font-size']='50%';
// $g->td->BGColor( "orange" );
 $g->Size(100,100);
 $g->Heading( 0, "One" );
 $g->Heading( 1, "Two" );
 $g->Heading( 2, "Three" );
 $g->Heading( 3, "Four" );

 $highlighter=new GridPen;
 $highlighter->operation=3;
 $highlighter->bg->SetRGB(1,1,0);
 $highlighter->fg->SetRGB(0,1,1);
 $highlighter->blend='=';

 $hi2=new GridPen;
 $hi2->operation=3;
 $hi2->fg->SetRGB(1,1,0);
 $hi2->bg->SetRGB(0,1,1);
 $hi2->blend='=';
 $g->EvenOddRow($highlighter,$hi2,4);

 $highlighter->operation=3;
 $highlighter->bg->SetRGB(0.9,0.1,1.0);
 $highlighter->fg->SetRGB(0.9,0.8,0.0);
 $highlighter->blend='-';
 $highlighter->value='XXX';

// var_dump($highlighter);

 $g->HLine( $highlighter, 0,3 );

 $highlighter->operation=2;
 $highlighter->blend='-';
 $highlighter->bg->SetRGB(1,0,1);
 $g->VLine( $highlighter, 4,0,3 );

// echo '<HR>[3,0]: '; var_dump($g->grid[3][0]);

 $highlighter->operation=2;
 $highlighter->blend='*';
 $highlighter->bg->SetRGB(1.0,0.3,1.0);
 $g->VLine( $highlighter, 3,0 );

// echo '<HR>[3,0]: '; var_dump($g->grid[3][0]);

// var_dump($g->grid[7][4]);

 $highlighter->operation=2;
 $highlighter->blend='+';
 $highlighter->bg->SetRGB(0.125,0.135,0.145);
 $g->Circle( $highlighter, 7, 4, 4 );

 $highlighter->operation=3;
 $highlighter->blend='=';
 $highlighter->bg->SetRGB(0.125,0.135,0.145);
 $highlighter->fg->SetRGB(0.825,0.835,0.845);
 $g->Circle( $highlighter, 50, 50, 20 );

 $highlighter->operation=1;
 $highlighter->blend='+';
 $highlighter->fg->SetRGB(0.75,0.75,0.75);

 $g->Rectangle( $highlighter, 15, 15, 4, 4 );

 $highlighter->operation=2;
 $highlighter->blend='-';
 $highlighter->bg->SetRGB(0.5,0.5,0.5);

 $g->Rectangle( $highlighter, 15, 15, 4, 4 );

 for ( $i=0; $i< $g->w; $i++ ) for ( $j=0; $j< $g->h; $j++ )
  $g->Value( $i, $j, "$i,$j" );

 $p->HTML('<h2>Grid size: '.$g->w.'x'.$g->h.'</h2>');

 $p->HTML($g->Render());

 $p->Render();

// var_dump($g);


<?php
 global $_ptree_uid;
 $_ptree_uid=0;
 function ReconstructTree($node,$root=TRUE) {
  if ( $root === TRUE ) {
   $id='tree.root';
  } else {
   global $_ptree_uid;
   $out='TreeNode '.($id='n'.$_ptree_uid).'=new TreeNode();';
   $_ptree_uid++;
   $out.=$id.'.content="'.$node['content'].'";';
   $out.=$id.'.reference="'.$node['reference'].'";';
   $out.=$root.'.Add('.$id.');
';
  }
  if ( is_array($node['children']) ) foreach ( $node["children"] as $child ) {
   $out.=ReconstructTree($child,$id);
  }
  return $out;
 }

 function EditableTree( &$p, $id, $table, $field, $value, $selection ) {
  $tree=json_decode($value,true);
  $reconstruction=ReconstructTree($tree);
  $p->JS('processing.min.js');
  $p->head[]=('<!--Processing.js Sketch--><script type="application/processing" data-processing-target="pjs">
int nodes=0;

// for drag/pan
boolean wasWithin=false;
int panX=10,panY=0;
int dx=0,dy=0;

class Tree {
 int levels[],renders[];
 int widest,level;
 int stroke,border,boldness;
 int fontSize;
 int nodeWidth,nodeSpace,nodeHeight,nodeVerticalSpace;
 TreeNode root,selected;
 Tree() {
  fontSize=10;
  nodeWidth=80;
  nodeSpace=10;
  nodeHeight=80;
  nodeVerticalSpace=80;
  selected=null;
  levels=new int[0];
  renders=new int[0];
  widest=0;
  stroke=2;
  border=2;
  boldness=1;
  root=new TreeNode();
  root.content="Start";
  root.reference="0";
  root.depth=1;
  root.tree=this;
 }
 String toJSON() {
  return "{"+root.toJSON()+"}";
 }
};

class TreeNode {
    Tree tree;
    TreeNode parent;
    String content,reference,data;
    int depth,parent,children;
    TreeNode[] node;
    TreeNode() {
     reference="";
     parent=0;
     id=0;
     content="Unnamed Node";
     data="";
     children=0;
     child=new int[0];
     node=new TreeNode[0];
     depth=1;
     parent=null;
    }
    TreeNode Add( TreeNode subtree ) {
     subtree.tree=this.tree;
     subtree.depth=this.depth+1;
     subtree.parent=this;
     TreeNode[] _node=new TreeNode[children+1];
     for ( int x=0; x < children; x++ ) {
      _node[x]=node[x];
     }
     _node[children]=subtree;
     children++;
     node=new TreeNode[children];
     for ( int x=0; x < children; x++ ) {
      node[x]=_node[x];
     }
     nodes++;
     return subtree;
    }
    void Remove( TreeNode child ) {
     TreeNode[] _node=new TreeNode[children-1];
     int y=0;
     for ( int x=0; x < children; x++ ) if ( node[x] != child ) {
      _node[y]=node[x];
      y++;
     }
     children--;
     if ( children < 0 ) children=0;
     node=new TreeNode[children];
     for ( int x=0; x < children; x++ ) {
      node[x]=_node[x];
     }
    }
    void _Widest() {
     tree.levels[this.depth]+=1;
     for ( int x=0; x<children; x++ ) this.node[x]._Widest();
    }
    int Widest() {
     tree.levels=new int[DepthUnder()+1];
     _Widest();
     int widest=0;
     for ( int x=0; x < tree.levels.length; x++ ) {
      if ( tree.levels[x] > widest ) widest=tree.levels[x];
     }
     return widest;
    }
    String nextNodeName() { return "node_"+(nodes+1); }
    int DepthUnder() {
     int max=this.depth;
     for ( int x=0; x<children; x++ ) {
      int branch=node[x].DepthUnder();
      if ( branch > max ) max=branch;
     }
     return max;
    }
    int Count() {
     int total=0;
     for ( int x=0; x<children; x++ ) total+=node[x].Count();
     return children+total;
    }
    // Render self at Center X, Top Y
    void RenderNode( int cx, int ty, boolean ParentSelected ) {
     int nx=cx-tree.nodeWidth/2;
     int tx=cx-(int)(textWidth(content)/2);
     int ny=ty;
     int nx2=(cx-tree.nodeWidth/2)+tree.nodeWidth;
     int ny2=ty+tree.nodeHeight;
     boolean within=(mouseX > panX+nx && mouseX < panX+nx2 && mouseY > ny && mouseY < ny2 );
     if ( within ) {
      wasWithin=true;
      if ( mousePressed ) tree.selected=this; 
      fill(200,200,255);
     } else {
      fill(255);
     }
     if ( tree.selected == this || ParentSelected ) {
      stroke(255,0,0);
      strokeWeight(tree.border*2);
     } else {
      stroke(127,127,127);
      strokeWeight(tree.border);
     }
     rect(panX+nx,ny,panX+nx2,ny2);
     fill(0);
     stroke(0);
     strokeWeight(tree.boldness);
     String name=content.replace(" (#","\n(#");
     text(name,panX+nx+4,ny+4,70,70);
    }
    void _Render( int LineY, int Width, int Place, TreeNode parent, int px, int py, boolean ParentSelected, float childRatio ) {
     if ( parent == null ) {
      offset=width/8.5;
      RenderNode( offset, LineY, false );
      for ( int i=0; i<children; i++ ) {
       node[i]._Render(LineY+tree.nodeVerticalSpace,children,i,this,offset,LineY,false,(float)(i)/(float)(children-1));
      }
     } else {
      int nx=(tree.nodeSpace+tree.nodeWidth)*Place+tree.nodeWidth/2;
      int ny=LineY;
      if ( tree.selected == this || ParentSelected ) {
       strokeWeight(tree.stroke+1);
       stroke(255,0,0);
      } else {
       strokeWeight(tree.stroke);
       stroke(127,127,127);
      }
      fill(255);
      line( panX+nx, ny, panX+px-(tree.nodeWidth/2)+(int)(tree.nodeWidth*childRatio), py+tree.nodeHeight );
      RenderNode( nx, ny, ParentSelected||(tree.selected==this) );
      tree.renders[this.depth]++;
      for ( int i=0; i<children; i++ ) {
       node[i]._Render(
        LineY+tree.nodeHeight+tree.nodeVerticalSpace,
        tree.levels[node[i].depth],tree.renders[node[i].depth],
        this,nx,ny,
        ParentSelected||(tree.selected==this),
        (float)i/(float)children
       );
      }
     }
    }
    void Render( int LineY ) {
      tree.widest=Widest();
      tree.renders=new int[tree.levels.length];
      for ( int x=0; x<tree.levels.length; x++ ) tree.renders[x]=0;
      _Render(LineY,1,0,null,0,0,false,0.5);
    }
    void toJSON() {
     childnodes="[";
     for ( int x=0; x<children; x++ ) childnodes+="{"+node[x].toJSON()+"}"+(x!=children-1?",":"");
     childnodes+="]";
     return \'"data":"\'+data
      +\'","content":"\'+content
    +\'","reference":"\'+reference
     +\'","children":\'+childnodes;
    }
};
int i = 0;
Tree tree=new Tree();
void setup() {
    background(0,0,0,0);
    size(3000, 2000);
    smooth();
    textSize(10);
    rectMode(CORNERS);
    frameRate(15);
    strokeWeight(2);
    '.$reconstruction.'
}
void draw() {
 wasWithin=false;
 if ( mousePressed ) background(255,190,190,255);
 else background(0,0,0,0);
 tree.root.Render(32+panY);
 stroke(0);
 fill(0);
 text("Tree Depth: "+tree.root.DepthUnder(), 15, 15 ); 
 text("Tree Widest Level: "+tree.root.Widest(), 15, 30 ); 
 text("Nodes: "+(nodes+1), 15, 45 );
 if ( tree.selected ) text("Selected Nodes: "+(tree.selected.Count()+1),15,60);
 if ( mousePressed && !wasWithin ) {
  panX+=mouseX-pmouseX; if ( panX > 10 ) panX=10;
  panY+=mouseY-pmouseY; if ( panY > 0 ) panY=0;
  tree.selected=null;
 }
}
void ResetPan() {
 panX=10;
 panY=0;
}
void RemoveSelected() {
 if ( tree.selected != null ) {
  if ( tree.selected.parent != null ) {
   tree.selected.parent.Remove(tree.selected);
   tree.selected=null;
  }
 }
}
void DeltaSelected(content,reference) {
 if ( tree.selected != null ) {
  tree.selected.content=content;
  tree.selected.reference=reference;
 }
}
void AddChildNodeToSelected(content,reference) {
 if ( tree.selected != null ) {
  TreeNode a=new TreeNode;
  a.content=content;
  a.reference=reference;
  tree.selected.Add(a);
 }
}
String toJSON() { return tree.toJSON(); }
   </script><!--Processing.js Sketch-->
  ');
  global $database;
  $m = new $selection($database);
  $categories=$m->All();
  $categoryselector='<select id="categoryselector">';
  foreach ( $categories as $category ) {
   $categoryselector.='<option value="'.$category['ID'].'">'.$category['Name'].' (#'.$category['ID'].')</option>';
  }
  $categoryselector.='</select>';
  $p->HTML('<div><b>Curated Category Structure Editor</b> <div class="right"><small><em>Best viewed in widescreen mode</em></small></div></div><div>'
   .'<div class="canvas-container"><div class="pannable"><canvas id="pjs"></canvas></div>'
    .'<div class="canvas-overlay canvas-controls">'
     .'<div>'
     .'<div>'.$categoryselector.'</div><span class="buttonlink" id="addnode"><span class="fi-plus"></span> Add Child Category</span></div>'
     .'<div class="buttonlink" id="deltanode"><span class="fi-refresh"></span> Change to Category</div>'
     .'<div id="remnode" class="redbutton right"><span class="fi-x"></span> Remove Selected</div>'
     .'<div id="resetpan" class="buttonlink" title="Reset Panning"><span class="fi-arrows-in"></span></div>'
    .'</div>'
   .'</div>'
  );
  $p->JQ('
   $("#categoryselector").chosen({search_within:true});
   $("#resetpan").click(function(e){
    var processing = Processing.getInstanceById("pjs");
    processing.ResetPan();
   });
   $("#addnode").click(function(e){
    var processing = Processing.getInstanceById("pjs");
    var reference= $("#categoryselector").val();
    var name=$( "#categoryselector option:selected" ).text();
    processing.AddChildNodeToSelected(name,reference);
    $.ajax({
      method:"POST",
         url:"ajax.ptree.php",
        data:{json:processing.toJSON(),I:"'.$id.'",T:"'.$table.'",F:"'.$field.'"},
     success: function(d) { console.log("Saved tree.",d); }
    });
   });
   $("#remnode").click(function(e){
    var processing = Processing.getInstanceById("pjs");
    console.log("Removed");
    processing.RemoveSelected();
    $.ajax({
      method:"POST",
         url:"ajax.ptree.php",
        data:{json:processing.toJSON(),I:"'.$id.'",T:"'.$table.'",F:"'.$field.'"},
     success: function(d) { console.log("Saved tree.",d); }
    });
   });
   $("#deltanode").click(function(e){
    var processing = Processing.getInstanceById("pjs");
    var reference= $("#categoryselector").val();
    var name=$( "#categoryselector option:selected" ).text();
    processing.DeltaSelected(name,reference);
    $.ajax({
      method:"POST",
         url:"ajax.ptree.php",
        data:{json:processing.toJSON(),I:"'.$id.'",T:"'.$table.'",F:"'.$field.'"},
     success: function(d) { console.log("Saved tree.",d); }
    });
   });
  ');
 }

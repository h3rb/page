var labelType, useGradients, nativeTextSupport;

(function() {
  var ua = navigator.userAgent,
      iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
      typeOfCanvas = typeof HTMLCanvasElement,
      nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'),
      textSupport = nativeCanvasSupport 
        && (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
  //I'm setting this based on the fact that ExCanvas provides text support for IE
  //and that as of today iPhone/iPad current text support is lame
  labelType = (!nativeCanvasSupport || (textSupport && !iStuff))? 'Native' : 'HTML';
  nativeTextSupport = labelType == 'Native';
  useGradients = nativeCanvasSupport;
  animate = !(iStuff || !nativeCanvasSupport);
})();

var addNode;

var stree_global={};

function init_stree(json) {

    var subtree = json.children.pop();
    //end
    var removing = false;

 var st=new $jit.ST({
        'injectInto': 'stree',
        //add styles/shapes/colors
        //to nodes and edges
        
        //set overridable=true if you want
        //to set styles for nodes individually 
        Node: {
          overridable: true,
          type: 'square',
          align: 'center',
          width: 60,
          height: 20,
          color: '#FFF'
        },
        Edge: {  
          overridable: true,  
          color: '#23A4FF',  
          lineWidth: 0.4  
         },  
        //change the animation/transition effect
        transition: $jit.Trans.Quart.easeOut,
        
        onBeforeCompute: function(node){
            console.log("loading " + node.name);
        },
        
        onAfterCompute: function(node){
            console.log("done");
        },

        //This method is triggered on label
        //creation. This means that for each node
        //this method is triggered only once.
        //This method is useful for adding event
        //handlers to each node label.
        onCreateLabel: function(label, node){
            //add some styles to the node label
            label.id = node.id;
            label.innerHTML = node.name;
            var style = label.style;
            if ( node._depth != 0 ) {
             node.width=150; node.height=32;
             style.color="white";
             style.backgroundColor='#636';
             style.textAlign = 'center';
             style.verticalAlign = 'middle';
             style.width = "150px";
             style.height = "32px";
            } else {
             style.color='#FFF';
             style.backgroundColor = '#FA0';
             style.textAlign = 'center';
             style.width = "60px";
             style.height = "20px";
            }

            //Delete the specified subtree 
            //when clicking on a label.
            //Only apply this method for nodes
            //in the first level of the tree.
            if(node._depth == 1) {
                style.cursor = 'pointer';
                label.onclick = function() {
                    if(!removing) {
                        removing = true;
                        console.log("removing subtree...");  
                        //remove the subtree
                        st.removeSubtree(label.id, true, 'animate', {
                            hideLabels: false,
                            onComplete: function() {
                              removing = false;
                              console.log("subtree removed");   
                            }
                        });
                    }
                }
            };
        },
        //This method is triggered right before plotting a node.
        //This method is useful for adding style 
        //to a node before it's being rendered.
        onBeforePlotNode: function(node) {
            if (node._depth == 1) {
                node.data.$color = '#f77';
            }
        }
    });

    //load json data
    st.loadJSON(json);

    //compute node positions and layout
    st.compute();

    //optional: make a translation of the tree
    st.geom.translate(new $jit.Complex(-200, 0), "current");

    //Emulate a click on the root node.
    st.onClick(st.root);
}

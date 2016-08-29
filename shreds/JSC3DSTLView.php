<?php

 global $_stl_viewers;
 $_stl_viewers=0;
 function STLViewer( &$p, $filename, $w=400, $h=300 ) {
  global $_stl_viewers;
  $_stl_viewers++;
  $named='stlview_'.$_stl_viewers;

  if ( $_stl_viewers === 1 ) {
   $p->JS('jsc3d/jsc3d.js');
   $p->JS('jsc3d/jsc3d.touch.js');
   $p->JS('jsc3d/jsc3d.webgl.js');
  }

 $p->JS('
        var '.$named.'=null;
	function setRenderMode() { if ( '.$named.' == null ) return;
		var modes = document.getElementById("render_mode_list_'.$named.'");
                '.$named.'.setRenderMode(modes.value);
                if ( modes.value == "texturesmooth" ) {
			var scene = '.$named.'.getScene();
			if(scene) {
				var objects = scene.getChildren();
				for(var i=0; i<objects.length; i++)
					objects[i].isEnvironmentCast = true;
			}

                }
		'.$named.'.update();
	}
 ');

 $p->JQ('
			/**
			 * Create and initialize the viewer instance.
			 */
		 '.$named.' = new JSC3D.Viewer(document.getElementById("'.$named.'"), {
				SceneUrl: "'.$filename.'",
				InitRotationX: -75,
				InitRotationY:  45,
				InitRotationZ:  10,
				BackgroundColor1: "#FFAAFF",
				BackgroundColor2: "#380040",
				SphereMapUrl: "i/dullchrome.jpg",
                                Definition: "standard",
				RenderMode: "flat",
                                ModelColor: "#CAA618",
				Renderer: "webgl"
			});
			'.$named.'.init();
			'.$named.'.update();

			/**
			 * Change display definition.
			 */
			function changeDefinition(definition) {
				// No need for FSAA since Chrome already enables MSAA in WebGL by default.
				if(definition == "high" && '.$named.'.isWebGLEnabled() && JSC3D.PlatformInfo.browser == "chrome")
					definition = "standard";

				'.$named.'.setDefinition(definition);
				'.$named.'.update();
			}
        $("#render_mode_list_'.$named.'").on("click change",function(e){setRenderMode();});
 ');

 $p->HTML('
 <div style="width:'.$w.'px; margin:auto; position:relative; font-size: 9pt; color: #777777;">
  <canvas id="'.$named.'" style="border: 1px solid;" width="'.$w.'" height="'.$h.'" ></canvas>
  <div id="'.$named.'_tip" style="display:block; color:#fff; background-color:#000; height:auto; width:'.($w-11).'px; border-radius:5px; border:1px solid #777777; opacity:0.5; font:1em/1.4 Cambria,Arial,sans-serif; padding:6px;"> 
			Drag: rotate, Drag+SHIFT: zoom, Rendering mode: 
		<select id="render_mode_list_'.$named.'">
		<option value="flat">Flat</option>
		<option value="point">Point Cloud</option>
<!--		<option value="wireframe">Wireframe</option> -->
		<option value="smooth">Smoothed</option>
		<option value="texturesmooth">Chrome</option>
		</select>
     </div>
  ');
 }

<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="three.min.js"></script>
<script type="text/javascript" src="FirstPersonControls.js"></script>
<script type="text/javascript" src="PointerLockControls.js"></script>
<script type="text/javascript">

	var c = null;
	var xAim = 0;
	var yAim = 0;
	var zAim = 0;
//	var xPos = 0;
//	var yPos = 1000;
//	var zPos = 500;
	var xPos = 0;
	var yPos = -130;
//	var yPos = 0;
	var zPos = 0;
	var scene = null;
	var renderer = null;
	var linesToDo = [];

	function getNormal(vector) {
		sum = vector[0]+vector[1]+vector[2];
		
		return [vector[0]/sum,vector[1]/sum,vector[2]/sum];
	}

	function render() {
//		console.log("animating");
		
		var goCounter = 0;
		
		while(linesToDo.length > 0) {
			
			console.log("Popping");
			
			var material = new THREE.LineBasicMaterial({
				color: 0xFFFFFF
			});

			obj = linesToDo.shift();
//			obj = linesToDo.pop();
			var geometry = new THREE.Geometry();
			geometry.vertices.push(new THREE.Vector3(obj.start[0], obj.start[2], obj.start[1]));
			geometry.vertices.push(new THREE.Vector3(obj.end[0], obj.end[2], obj.end[1]));

			var line = new THREE.Line(geometry, material);
			scene.add(line);
			
			goCounter++;
			
			if(goCounter > 100) {
				break;
			}
		}
		
		
		renderer.render(scene, camera);
		controls.update();
		
		requestAnimationFrame(render);
	}

	$(document).ready(function() {

		scene = new THREE.Scene();
		renderer = new THREE.WebGLRenderer();

//		camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 1, 10000);
		camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 1, 1000);
		camera.position.set(xPos, yPos, zPos);
		camera.lookAt(new THREE.Vector3(xAim, yAim, zAim));
		var projector = new THREE.Projector();
		
		scene.add(camera);
		
		scene.add(projector);
		
		renderer.setSize( window.innerWidth, window.innerHeight );
		document.body.appendChild( renderer.domElement );

		controls = new THREE.PointerLockControls( camera );
//		controls = new THREE.FirstPersonControls( camera );
//                controls.movementSpeed = 70;
//                controls.lookSpeed = 0.05;
                controls.noFly = false;
//                controls.lookVertical = false;
		controls.enabled = true;
		
		scene.add(controls.getObject());
//		scene.add(controls);

		requestAnimationFrame(render);

		$.ajax({
			url: "testBSP.php",
			dataType: "json",
			success: function(json) {
				
				console.log("Loaded JSON");
				
				
				
				var counter = 0;
				
				linesToDo = json;
				
//				$.each(json, function(key, obj) {
//					
//					var geometry = new THREE.Geometry();
//					geometry.vertices.push(new THREE.Vector3(obj.start[0], obj.start[2], obj.start[1]));
//					geometry.vertices.push(new THREE.Vector3(obj.end[0], obj.end[2], obj.end[1]));
////					geometry.vertices.push(new THREE.Vector3(100, 100, 1000));
//					
////					camera.lookAt(new THREE.Vector3(obj.start[0], obj.start[1], obj.start[2]));
//					
//					var line = new THREE.Line(geometry, material);
//					scene.add(line);
//					
//					counter++;
//
//					if(counter > 10) {
////						return false;
//					}
//					
//				});
				
				renderer.render(scene, camera);

				console.log("Done!");
				
			}
		});
		
	});


</script>
<!--<canvas id="newCanvas" width="800" height="600" style="border:1px solid #000000;"></canvas>-->
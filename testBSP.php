<?php

ini_set('memory_limit','2048M');

$fileHandler = fopen("/home/christopher/NetBeansProjects/BSPGraphics/gm_flatgrass.bsp","r");
//$fileHandler = fopen("/home/christopher/NetBeansProjects/BSPGraphics/d1_trainstation_01.bsp","r");
//$fileHandler = fopen("/home/christopher/NetBeansProjects/BSPGraphics/gm_construct.bsp","r");
//$fileHandler = fopen("/home/christopher/NetBeansProjects/BSPGraphics/gm_construct_flatgrass_v5.bsp","r");
//$fileHandler = fopen("/home/christopher/NetBeansProjects/BSPGraphics/ctf_2fort.bsp","r");
//$fileHandler = fopen("/home/christopher/NetBeansProjects/BSPGraphics/cs_office.bsp","r");

$ident = bytesBetween(0,4);
$version = bytesBetween(4,8);

$planes = array();
$faces = array();
$surfEdges = array();
$edges = array();
$vertices = array();

for($i = 0 ; $i < 64 ; $i++) {

	getLumpT($i,$i*16+8);
}

$maxX = 0;

//foreach($planes as $plane) {
//	
//	$mapPosition = getMapPosition($plane['dist'], $plane['vector']);
//	
////	echo "Map position: ".implode(",",  $mapPosition)."\n";
//	
//	if($mapPosition[0] > $maxX) {
//		$maxX = $mapPosition[0];
//	}
//	
//	$left = $mapPosition[0]/50+400;
//	$top = $mapPosition[1]/50+400;
//	
//	echo "<div style=\"position:fixed;width:10px;height:10px;background-color:".getRandomColour().";left:{$left}px;top:{$top}px;\"></div>";
//}

$polyPositions = array();

foreach($faces as $face) {
	
	$plane = $planes[$face['planeNum']];
	
	$firstEdge = $face['firstEdge'];

	for($i = 0 ; $i < $face['numEdges']; $i++) {
		
			$surfEdge = $surfEdges[$i+$firstEdge];
			$surfEdge = abs($surfEdge);
			
			$startPoint = $vertices[$edges[$surfEdge][0]];
			$endPoint = $vertices[$edges[$surfEdge][1]];

			$polyPositions[] = array(
			    "start" => $startPoint,
			    "end" => $endPoint
			);
			
//			echo $face['planeNum'].": (".implode(", ",$startPoint).") -> (".implode(", ",$endPoint).")\n";
			
//			foreach($edges[$surfEdge] as $edgeCell) {
//
////				die(var_dump($edgeCell));
//				
////				$left = $vertices[$edgeCell][0]/50+400;
////				$top = $vertices[$edgeCell][1]/50+400;
////				
////				echo "<div xPos=\"{$vertices[$edgeCell][0]}\" yPos=\"{$vertices[$edgeCell][1]}\" zPos=\"{$vertices[$edgeCell][2]}\" style=\"position:fixed;width:5px;height:5px;background-color:".getRandomColour().";left:{$left}px;top:{$top}px;\"></div>\n";
//			}
	}
	
//	die;
}

foreach($polyPositions as $key => $position) {
	
	foreach($polyPositions[$key] as $key2 => $startEnd) {
	
		foreach($polyPositions[$key][$key2] as $key3 => $vectorAxis) {
			$polyPositions[$key][$key2][$key3] = $polyPositions[$key][$key2][$key3] * 0.01;
		}
	}
}

$json = json_encode($polyPositions);

die($json);

//$size = memory_get_usage();
//$unit=array('b','kb','mb','gb','tb','pb');
//die(var_dump(round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]));

function getRandomColour() {
	
//	return "rgb(".rand(0, 255).",".rand(0, 255).",".rand(0, 255).")";
	return "black";
}

function getAngle($distanceVector) {
	
}

function getMapPosition($distance, $vertex) {
	
	foreach($vertex as $key => $axis) {
		$vertex[$key] = $axis * $distance;
	}
	
	return $vertex;
}

function getLumpT($lumpVersion,$startPos) {
	
	$fileOffset = getInt(bytesBetween($startPos,$startPos+4));
	$fileSize = getInt(bytesBetween($startPos+4,$startPos+8));
	$version = getInt(bytesBetween($startPos+8,$startPos+12));
	$fourCC = getInt(bytesBetween($startPos+12,$startPos+16));
	
	if($lumpVersion == 1) {
		
		global $planes;
		
		$numberOfPlanes = $fileSize / 20;
		
		for($i = 0 ; $i < $numberOfPlanes; $i++) {
			$planes[] = getDplaneT($fileOffset+($i*20));
		}

	} elseif($lumpVersion == 7) {
		
		$numberOfPlanes = $fileSize / 56;
		
		global $faces;
		
		for($i = 0 ; $i < $numberOfPlanes; $i++) {
		
			$faces[] = getDFaceT($fileOffset+($i*56));
		}
		
//		die(var_dump($faces));
		
	} elseif($lumpVersion == 13) {
		
		global $surfEdges;
		
		$numberOfPlanes = $fileSize / 4;

		for($i = 0 ; $i < $numberOfPlanes; $i++) {
//			$surfEdges[] = getVector($fileOffset+($i*12));
			$surfEdges[] = getInt(bytesBetween($fileOffset+($i*4),$fileOffset+4+($i*4)));
		}

	} elseif($lumpVersion == 12) {
		
		global $edges;
	
		$numberOfPlanes = $fileSize / 4;
		
		for($i = 0 ; $i < $numberOfPlanes; $i++) {
			$edges[] = array(
			    getUnsignedShort(bytesBetween($fileOffset+($i*4),$fileOffset+($i*4)+2)),
			    getUnsignedShort(bytesBetween($fileOffset+($i*4)+2,$fileOffset+($i*4)+4))
			);
		}
		
	} elseif($lumpVersion == 3) {
		
		global $vertices;
		$numberOfPlanes = $fileSize / 12;
		
		for($i = 0 ; $i < $numberOfPlanes; $i++) {
			$vertices[] = getVector($fileOffset+($i*12));
		}

	}
}


function getDFaceT($startPos) {
	
	$dface = array();
	$dface['planeNum'] = getUnsignedShort(bytesBetween($startPos, $startPos+2));
	$dface['side'] = bytesBetween($startPos+2, $startPos+3);
	$dface['onNode'] = bytesBetween($startPos+3, $startPos+4);
	$dface['firstEdge'] = getShort(bytesBetween($startPos+4,$startPos+8));
	$dface['numEdges'] = getShort(bytesBetween($startPos+8,$startPos+10));
	$dface['texInfo'] = getShort(bytesBetween($startPos+10,$startPos+12));
	$dface['dispInfo'] = getShort(bytesBetween($startPos+12,$startPos+14));
	$dface['surfaceFogVolumeId'] = getShort(bytesBetween($startPos+14,$startPos+16));
	$dface['styles'] = (bytesBetween($startPos+16,$startPos+20));
	$dface['lightOffset'] = getInt(bytesBetween($startPos+20,$startPos+24));
	$dface['area'] = getFloat(bytesBetween($startPos+24,$startPos+28));
	// unneeded lightmap text = +16
	$dface['origFace'] = getInt(bytesBetween($startPos+44,$startPos+48));
	$dface['numPrims'] = getUnsignedShort(bytesBetween($startPos+48,$startPos+50));
	$dface['firstPrimId'] = getUnsignedShort(bytesBetween($startPos+50,$startPos+52));
	$dface['smoothingGroups'] = getUnsignedInt(bytesBetween($startPos+52,$startPos+56));
	
	return $dface;
}

function getShort($bytes) {
	return unpack('s',$bytes)[1];
}

function getUnsignedShort($bytes) {
	
	return unpack('S',$bytes)[1];
}

function getUnsignedInt($bytes) {
	return unpack('I',$bytes)[1];
}

function getDplaneT($startPos) {
	
	$plane = array();
	$plane['vector'] = getVector($startPos);
	$plane['dist'] = getFloat(bytesBetween($startPos+12,$startPos+16));
	$plane['type'] = getInt(bytesBetween($startPos+16,$startPos+20));
	
	return $plane;
}

function getVector($startPos) {
	
	return array(
	    getFloat(bytesBetween($startPos,$startPos+4)),
	    getFloat(bytesBetween($startPos+4,$startPos+8)),
	    getFloat(bytesBetween($startPos+8,$startPos+12)),
	);
}

function getFloat($bytes) {

	return unpack('f',$bytes)[1];
}

function getInt($bytes) {
	
//	$intVal = 0;
//	
////	foreach(str_split($bytes) as $byte) {
////		$intVal = $intVal << 8;
////		$intVal += ord($byte);
////	}
//	
//	$revBytes = array();
//	
//	foreach(str_split($bytes) as $byte) {
//		array_unshift($revBytes,$byte);
//	}
//
//	foreach($revBytes as $byte) {
//		$intVal = $intVal << 8;
//		$intVal += ord($byte);
////		echo "Bytes: ".ord($byte).", Intval: ".$intVal."\n";
//	}
	
//	return $intVal;
	return unpack('i',$bytes)[1];;
}

function bytesBetween($start, $end) {
	
	global $fileHandler;
	
	fseek($fileHandler, $start);

	return fread($fileHandler, $end-$start);
}
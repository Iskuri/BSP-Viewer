<?php 

$modelString = file_get_contents("oildrum001.mdl");
$modelString = file_get_contents("oildrum001.vvd");

$modelArray = str_split($modelString);


// MDL FILE HEADER STUFF
//$id = bytesBetween(0, 4);
//$version = bytesBetween(4, 8);
//$name = bytesBetween(8,72);
//$dataLength = bytesBetween(72, 76);
//
//bytesBetween(76, 88);
//bytesBetween(88, 100);
//bytesBetween(100, 112);
//bytesBetween(112, 124);
//bytesBetween(124, 136);
//bytesBetween(136, 148);
//
//$flags = bytesBetween(148, 152);
//
//die(var_dump($name));

function bytesBetween($start, $end) {
	
	global $modelArray;
	
	$string = "";
	
	for($i = $start; $i < $end; $i++) {
		$string .= $modelArray[$i];
	}
	
	return $string;
}
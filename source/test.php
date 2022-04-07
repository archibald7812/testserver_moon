<?php
include 'example.php';
$value = new SimpleXMLElement($xmlstr);
$cntP = count($value->body->p);
for ($i = 0; $i < $cntP; $i++) {
	$array[$i] = $value->body->p[$i];
	if ($array[$i]->r[1]) {
		$arrayR[$i] = $array[$i];
	}
}
$cntR = count($arrayR);
$keys = array_keys($arrayR);
$cntK = count($keys);
for ($n = 0; $n < $cntK; $n++) {
	for ($j = 1; isset($arrayR[$keys[$n]]->r[$j]);) {
		(string)$arrayR[$keys[$n]]->r[0]->t[0] .= (string)$arrayR[$keys[$n]]->r[$j]->t[0];
		unset($arrayR[$keys[$n]]->r[$j]);
	}
}
for ($i = 0; $i < $cntP; $i++) {
	$array = array_replace($array, $arrayR);
	$names[$i] = (string)$array[$i]->r->t;
	$names[$i] = "'" . $names[$i] . "',";
}
///////////////

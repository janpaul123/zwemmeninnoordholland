<?php
include('Shapefile.inc.php');
$options = array('noparts' => false);
$shp = new ShapeFile("zwemwaterlox.shp", $options); // along this file the class will use file.shx and file.dbf

$i = 0;
while ($record = $shp->getNext() and $i<99999) {
	echo "<pre>"; // just to format
	$dbf_data = $record->getDbfData();
	$shp_data = $record->getShpData();
	//Dump the information
	var_dump($dbf_data);
	var_dump($shp_data);
	$i++;
	echo "</pre>";
}
<?php
include('common.php');
include('Shapefile.inc.php');

// load shapefile
$shpf = new ShapeFile("zwemwaterlox.shp", array('noparts' => false));

$strings = array(
	'CBS_NR' => 'cbs',
	'NAAM' => 'naam',
	'ZWEMFOLDER' => 'zwemfolder',
	'OPMERKING' => 'opmerking',
	'OPMERKING2' => 'advies',
	'id' => 'id',
);

$booleans = array(
	'DRIJFL' => 'drijflijn',
	'HELLING' => 'aflopend',
	'ZAND' => 'zandstrand',
	'WC' => 'toiletten',
	'DOUCHE' => 'douches',
	'REST_' => 'restaurant',
	'VRIJ_' => 'toegankelijk',
	'OV' => 'ov',
	'P' => 'parkeerplaats',
	'DIER' => 'huisdieren',
	'EHBO' => 'ehbo',
);


// oude locaties verwijderen
$stm = $db->prepare('TRUNCATE locaties');
if (!$stm->execute()) print_r($stm->errorInfo());

// array vullen met shapefile data
$data = array();
$zwemfoldernrs = array(); // array met alle zwemfolder nummers
while ($record = $shpf->getNext()) {
	$dbf = $record->getDbfData(); // database data
	$shp = $record->getShpData(); // shape data
	
	// doe een eerste check op geldige waardes
	if (isset($shp['x']) && isset($shp['y'])) {
		$id = strtolower(preg_replace('/[^0-9A-Za-z]/', '', $dbf['ZWEMFOLDER'] . $dbf['NAAM'] . $dbf['CBS_NR']));
		
		$data[$id] = array();
		
		$zwemfoldernrs[trim(strtolower($dbf['ZWEMFOLDER']))] = true;
		
		foreach ($dbf as $key => $value) {
			// spaties aan begin en eind van strings verwijderen
			if (is_string($value)) {
				$value = trim($value);
			}
			$data[$id][$key] = $value;
		}
		
		// vul x en y punten uit de shape data ook toe
		$data[$id]['x'] = $shp['x'];
		$data[$id]['y'] = $shp['y'];
		$data[$id]['id'] = $id;
	}
}

// sorteer op zwemfolder
function cmp($a, $b){ return strnatcmp($a['ZWEMFOLDER'], $b['ZWEMFOLDER']); }
uasort($data, 'cmp');

// voeg nieuwe data toe in de database
foreach ($data as $dbf) {
	if (strtolower($dbf['OPMERKING2']) != 'vervallen' && strtolower($dbf['OPMERKING2']) != 'cluster' && !isset($zwemfoldernrs[strtolower($dbf['ZWEMFOLDER']) . 'a']))
	{
		// ga uit van het hoogste giftigheidsniveau, verfijn dan op basis van de inhoud van OPMERKING2
		$giftig = 2;
		if (strtolower($dbf['OPMERKING2']) == 'zwemlocatie') $giftig = 0;
		if (substr(strtolower($dbf['OPMERKING2']), 0, 12) == 'waarschuwing') $giftig = 1;
		
		if ($giftig == 0) {
			$dbf['OPMERKING2'] = '';
		}
		
		$dbf['CBS_NR'] = ucwords(strtolower($dbf['CBS_NR']));
		
		//print_r($dbf);
		
		$sql = 'INSERT INTO locaties(loc, gloc, doorzicht, giftig, ' . join(', ', array_merge($strings, $booleans)) . ')' .
			' VALUES (ST_PointFromText(:loc, 28992), ST_TRANSFORM(ST_PointFromText(:loc, 28992), 4326), :doorzicht, :giftig,' .
			' :' . join(', :', array_merge($strings, $booleans)) . ');';
			
		$stm = $db->prepare($sql, array(PDO::PGSQL_ATTR_DISABLE_NATIVE_PREPARED_STATEMENT => true));
		
		$stm->bindValue(':doorzicht', $dbf['DOORZ'], PDO::PARAM_INT);
		$stm->bindValue(':giftig', $giftig, PDO::PARAM_INT);
		$stm->bindValue(':loc', 'POINT(' . intval($dbf['x']) . ' ' . intval($dbf['y']) . ')', PDO::PARAM_STR);
		
		foreach ($strings as $key => $value) {
			$stm->bindValue(':' . $value, $dbf[$key], PDO::PARAM_STR);
		}
		foreach ($booleans as $key => $value) {
			$stm->bindValue(':' . $value, ($dbf[$key] == 'ja' ? 'true' : 'false' ), PDO::PARAM_STR);
		}
		
		if (!$stm->execute()) print_r($stm->errorInfo());
		
		echo "{$dbf['ZWEMFOLDER']}: {$dbf['NAAM']}, {$dbf['CBS_NR']}<br/>";
	}
}

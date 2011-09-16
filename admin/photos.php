<?php
include('../common.php');

$locaties = array();

$stm = $db->query('SELECT *, ST_X(gloc) AS y, ST_Y(gloc) AS x FROM locaties ORDER BY naam ASC');

if (!$stm->execute()) die('query failed...');

while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
	$locaties[$row['id']] = $row;
}

// sorteer op zwemfolder
function cmp($a, $b){ return strnatcmp($a['zwemfolder'], $b['zwemfolder']); }
uasort($locaties, 'cmp');

$delta = 0.003;
$i = 0;

$photosSql = 'INSERT INTO photos(id, owner_id, owner_name) VALUES (:photo_id, :owner_id, :owner_name)';
$locatiePhotoSql = 'INSERT INTO locatie_photos(locatie_id, photo_id) VALUES (:locatie_id, :photo_id)';

foreach ($locaties as $locatie_id => $locatie) {
	$minx = $locatie['y']-$delta;
	$maxx = $locatie['y']+$delta;
	$miny = $locatie['x']-$delta;
	$maxy = $locatie['x']+$delta;
	
	$url = "http://www.panoramio.com/map/get_panoramas.php?set=public&from=0&to=10&minx={$minx}&miny={$miny}&maxx={$maxx}&maxy={$maxy}&size=mini_square&mapfilter=true";
	
	$photos = json_decode(file_get_contents($url))->photos;
	
	foreach ($photos as $photo) {
		$photoStm = $db->prepare($photosSql, array(PDO::PGSQL_ATTR_DISABLE_NATIVE_PREPARED_STATEMENT => true));
		$photoStm->bindValue(':photo_id', $photo->photo_id, PDO::PARAM_INT);
		$photoStm->bindValue(':owner_id', $photo->owner_id, PDO::PARAM_INT);
		$photoStm->bindValue(':owner_name', $photo->owner_name, PDO::PARAM_STR);
		try {
			$photoStm->execute();
		} catch (PDOException $e) {}
		
		$locatiePhotoStm = $db->prepare($locatiePhotoSql, array(PDO::PGSQL_ATTR_DISABLE_NATIVE_PREPARED_STATEMENT => true));
		$locatiePhotoStm->bindValue(':locatie_id', $locatie_id, PDO::PARAM_STR);
		$locatiePhotoStm->bindValue(':photo_id', $photo->photo_id, PDO::PARAM_INT);
		try {
			$locatiePhotoStm->execute();
		} catch (PDOException $e) {}
	}
	
	echo '<p>';
	echo "<h2>{$locatie['naam']} te {$locatie['cbs']}</h2>";
	foreach ($photos as $photo) {
		echo "<img src='{$photo->photo_file_url}'/>";
	}
	echo '</p>';
	
	//if (++$i > 0) break;
}

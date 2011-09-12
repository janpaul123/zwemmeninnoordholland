<?php
include('common.php');

$locaties = array();

$stm = $db->query('SELECT *, ST_X(gloc) AS y, ST_Y(gloc) AS x FROM locaties ORDER BY naam ASC');

if ($stm->execute()) {
while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
	$locaties[$row['id']] = $row;
}

// sorteer op zwemfolder
function cmp($a, $b){ return strnatcmp($a['zwemfolder'], $b['zwemfolder']); }
uasort($locaties, 'cmp');

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<link href="sen.full.min.css" rel="stylesheet" type="text/css">
	<link href="style.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true"></script>
	<script type="text/javascript" src="label.js"></script>
	<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.6.3.js"></script>
	<script type="text/javascript" src="placeholder.js"></script>
	<script type="text/javascript">
var markers = new Array();
var center = new google.maps.LatLng(52.667063, 4.886169);
var indebuurt;

function maakKnop(text, title) {
	// Create a div to hold the control.
	var controlDiv = document.createElement('DIV');
	
	// Set CSS styles for the DIV containing the control
	// Setting padding to 5 px will offset the control
	// from the edge of the map
	controlDiv.style.padding = '5px';
	
	// Set CSS for the control border
	var controlUI = document.createElement('DIV');
	controlUI.className = 'controlUI';
	controlUI.title = title;
	controlDiv.appendChild(controlUI);
	
	// Set CSS for the control interior
	var controlText = document.createElement('DIV');
	controlText.className = 'controlText';
	controlText.innerHTML = text;
	controlUI.appendChild(controlText);
	
	return controlDiv;
}

$(document).ready(function() {
	var map = new google.maps.Map(document.getElementById("map_canvas"), {
		zoom: 9,
		minZoom: 8,
		maxZoom: 15,
		center: center,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		streetViewControl: false,
		mapTypeControl: false
	});
	
	var overzicht = maakKnop('Overzicht', 'Toon de overzichtskaart');
	overzicht.index = 2;
	map.controls[google.maps.ControlPosition.TOP_RIGHT].push(overzicht);
	
	google.maps.event.addDomListener(overzicht, 'click', function() {
		map.setZoom(9);
		map.panTo(center);
	});

	
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			indebuurt = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			
			var buurtknop = maakKnop('In de buurt', 'Toon locaties in de buurt');
			buurtknop.index = 1;
			map.controls[google.maps.ControlPosition.TOP_RIGHT].push(buurtknop);
			
			google.maps.event.addDomListener(buurtknop, 'click', function() {
				map.setZoom(11);
				map.panTo(indebuurt);
			});
		});
		navigator.geolocation.watchPosition(function(position) {
			indebuurt = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
		});
	}
	
	var locaties = [
		<?php
			foreach ($locaties as $id => $locatie) {
				echo "['$id', {$locatie['x']}, {$locatie['y']}, {$locatie['giftig']}, '{$locatie['zwemfolder']}'],\n";
			}
		?>
	];
	
	var flags = [
		new google.maps.MarkerImage('flag_0.png', new google.maps.Size(16, 16), new google.maps.Point(0,0), new google.maps.Point(10, 14)),
		new google.maps.MarkerImage('flag_1.png', new google.maps.Size(16, 16), new google.maps.Point(0,0), new google.maps.Point(10, 14)),
		new google.maps.MarkerImage('flag_2.png', new google.maps.Size(16, 16), new google.maps.Point(0,0), new google.maps.Point(10, 14)),
	];
		
	for (var i in locaties) {
		var locatie = locaties[i];
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(locatie[1], locatie[2]),
			map: map,
			icon: flags[locatie[3]],
			zIndex: locatie[3],
			text: locatie[4],
			elid: locatie[0]
		});
		var label = new Label({
			map: map
		});
		label.bindTo('position', marker, 'position');
		label.bindTo('text', marker, 'text');
		google.maps.event.addListener(marker, 'click', function() {
			var $id = $('#' + this.elid);
			$('html,body').animate({scrollTop: $id.offset().top - $(window).height()/3}, 1000);
			$('.locatie.selected').removeClass('selected');
			$id.addClass('selected');
		});
		markers[locatie[0]] = marker;
		
		$('#' + locatie[0]).click(function() {
			var marker = markers[$(this).attr('id')];
			if (map.getZoom() < 12) map.setZoom(12);
			map.panTo(marker.getPosition());
			$('.locatie.selected').removeClass('selected');
			$(this).addClass('selected');
		});	
	}
	
	$('#meer').click(function(e) {
		$('.verberg').show('fast');
		$('#minderli').show();
		$('#meerli').hide();
		e.preventDefault();
	});
	
	$('#minder').click(function(e) {
		$('.verberg').hide('fast');
		$('#minderli').hide();
		$('#meerli').show();
		e.preventDefault();
	});
	
	$('#menu a').click(function(e) {
		$('#content > div').hide();
		$($(this).attr('id') + '_content').show();
		$('#menu a').removeClass('selected');
		$(this).addClass('selected');
		e.preventDefault();
	});
});
</script>

</head>
<body>

<div id="page">
	<div id="top">
		<h1>Veilig zwemmen in Noord-Holland</h1>
		<ul id="menu">
			<li><a id="informatie" class="selected" href="#">Informatie</a></li>
			<li><a id="nieuws" href="#">Nieuws</a></li>
			<li><a id="kaart" href="#">Kaart</a></li>
			<li><a id="legenda" href="#">Legenda</a></li>
			<li><a id="gezondheid" href="#">Gezondheid</a></li>
		</ul>
	</div>
	<div id="content">
		<div id="kaart_content">
			<div id="map"><div id="map_canvass"></div></div>
			
			<div id="rechts">
				<div id="info">
					
					
					<p>
						Op deze site vindt u een overzicht van alle open lucht zwemlocaties in de provincie
						Noord Holland. Omdat veiligheid voorop staat kunt u de kwaliteit van het water per locatie
						aflezen aan de kleuren rood (negatief zwemadvies) en geel (waarschuwing). Deze waterkwaliteit
						wordt in het zwemwaterseizoen (van 1 mei tot 1 oktober) elke twee weken door de provincie
						gemeten.
					</p>
					
					<p>
						De nummering en symbolen die gebruikt worden op deze site komen overeen met de zwemwaterfolder
						die wordt uitgegeven door de provincie. In deze folder vindt u bovendien meer informatie
						over gezondheidsklachten en risico's van het buiten zwemmen. Voor meer informatie belt
						u de zwemwatertelefoon <strong itemprop="tel" class="tel">0800 9986734</strong> (gratis) of kijkt
						u op NOS teletekst pagina <strong>725</strong>.
					</p>
					
					<p>
						<a href="http://www.noord-holland.nl/web/file?uuid=e6b762bc-451f-4173-989d-90458cdf64ac&owner=f22bc2f4-2ebd-4086-8aa8-7e9c95211aca&contentid=809">Download zwemwaterfolder 'Veilig zwemmen' (2011)</a>
					</p>
					
					<p>
						Als u op de hoogte wilt worden gehouden van nieuws omtrent zwemmen en veiligheid, vul
						dan hier uw e-mail adres in en klik op <strong>Abonneer</strong>:
					</p>
					
					<div id="formulier">
						<input name="email" id="email" type="email" placeholder="E-mail adres"/>
						<button id="submit" type="submit">Abonneer</button>
					</div>
					
					<br/>
					
					De laatste berichten staan hier nog eens ter referentie:
					<ul>
						<li><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemwaterkwaliteit.htm" target="_blank">9 september 2011</a></li>
						<li><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemverbod-Oud-Valkeveen-opgeheven.htm" target="_blank">6 september 2011</a></li>
						<li><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemverbod-Oud-Valkeveen-te-Naarden.htm" target="_blank">1 september 2011</a></li>
						<li class="verberg"><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Westeinderplassen-Vrouwentroost-Aalsmeer-zwemverbod-opgeheven.htm" target="_blank">31 augustus 2011</a></li>
						<li class="verberg"><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemverbod-Westeinderplassen-Vrouwentroost-in-Aalsmeer.htm" target="_blank">26 augustus 2011</a></li>
						<li class="verberg"><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Waarschuwing-blauwalg-Muiderberg-en-Naarderbos-ingetrokken.htm" target="_blank">16 augustus 2011</a></li>
						<li class="verberg"><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemverbod-Muiderberg-en-Almeerderstrand-opgeheven.htm" target="_blank">18 juli 2011</a></li>
						<li class="verberg"><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemverbod-voor-Ursemmerplas-opgeheven.htm" target="_blank">7 juli 2011</a></li>
						<li class="verberg"><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemverbod-Ursemmerplas-ingesteld.htm" target="_blank">29 juni 2011</a></li>
						<li class="verberg"><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemverbod-locatie-De-Kuifeend-Jagersveld.htm" target="_blank">23 juni 2011</a></li>
						<li class="verberg"><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemverbod-strand-Muiderberg-langer-van-kracht.htm" target="_blank">22 juni 2011</a></li>
						<li id="meerli"><a href="#" id="meer">meer...</a></li>
						<li id="minderli"><a href="#" id="minder">minder...</a></li>
					</ul>
				</div>
				<div id="lijst">
				<?php
					$attrlijst = array(
						'drijflijn' => array('drijflijn', 'geen drijflijn'),
						'aflopend' => array('geleidelijk aflopende onderwaterbodem', 'geen geleidelijk aflopende onderwaterbodem'),
						'zandstrand' => array('zandstrand', 'geen zandstrand'),
						'toiletten' => array('toiletten', 'geen toiletten'),
						'douches' => array('douches', 'geen douches'),
						'restaurant' => array('restaurant, paviljoen, snackbar', 'geen restaurant, paviljoen, snackbar'),
						'toegankelijk' => array('vrij toegankelijk', 'niet vrij toegankelijk'),
						'ov' => array('bereikbaar met openbaar vervoer', 'niet bereikbaar met openbaar vervoer'),
						'parkeerplaats' => array('parkeergelegenheid', 'geen parkeergelegenheid'),
						'huisdieren' => array('huisdieren toegestaan', 'huisdieren niet toegestaan'),
						'ehbo' => array('EHBO', 'geen EHBO'),
					);
					$doorzichtlegenda = array(
						0 => 'doorzicht meer dan 1 meter',
						1 => 'doorzicht 0,5 tot 1 meter',
						2 => 'doorzicht minder dan 0,5 meter',
					);
					
					foreach ($locaties as $id => $locatie) {
						//print_r($locatie);
						$doorzicht = $doorzichtlegenda[$locatie['doorzicht']];
						$attributen = "<div class='attribuut doorzicht_{$locatie['doorzicht']}' title='{$doorzicht}' alt='{$doorzicht}'></div>";
						foreach ($attrlijst as $naam => $omschrijving) {
							if ($locatie[$naam] == 1) $attributen .= "<div class='attribuut {$naam}' title='{$omschrijving[0]}' alt='{$omschrijving[0]}'></div>";
							else $attributen .= "<div class='attribuut {$naam}_niet' title='{$omschrijving[1]}' alt='{$omschrijving[1]}'></div>";
						}
						
						$cbs_groot = strtoupper($locatie['cbs']);
						
						echo "
							<div id='$id' class='locatie giftig_{$locatie['giftig']}'>
								<div class='binnenin'>
									<div class='advies'>{$locatie['advies']}</div>
								</div>
								<h2>{$locatie['naam']}</h2>
								<div class='zwemfolder'>{$locatie['zwemfolder']}</div>
								<div class='attributen'>{$attributen}</div>
								<div class='cbs'>{$cbs_groot}</div>
							</div>
						";
					}
				}
				?>
				</div>
			</div>
		</div>
	</div>
</div>

</body>
</html>
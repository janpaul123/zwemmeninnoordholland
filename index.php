<?php
include('./common.php');

$locaties = array();

$stmLocaties = $db->query('SELECT *, ST_X(gloc) AS y, ST_Y(gloc) AS x FROM locaties');
$stmLocaties->execute();
while ($row = $stmLocaties->fetch(PDO::FETCH_ASSOC)) {
	$locaties[$row['id']] = $row;
	$locaties[$row['id']]['photos'] = array();
}

$stmPhotos = $db->query('SELECT locatie_id, photos.* FROM locatie_photos JOIN photos ON locatie_photos.photo_id = photos.id ORDER BY score DESC');
$stmPhotos->execute();
while ($row = $stmPhotos->fetch(PDO::FETCH_ASSOC)) {
	$locaties[$row['locatie_id']]['photos'][] = $row;
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
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox-1.3.4.css" media="screen" />
	<link href="style.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">
		var locaties = [
			<?php
				foreach ($locaties as $id => $locatie) {
					echo "['$id', {$locatie['x']}, {$locatie['y']}, {$locatie['giftig']}, '{$locatie['zwemfolder']}', [";
					
					$i = 0;
					foreach ($locatie['photos'] as $photo) {
						if (++$i > 8) break;
						$photo['owner_name'] = json_encode($photo['owner_name']);
						echo "[{$photo['id']}, {$photo['owner_id']}, {$photo['owner_name']}],";
					}
					
					echo "]],\n";
				}
			?>
		];
	</script>
	
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=panoramio&sensor=true"></script>
	<script type="text/javascript" src="label.js"></script>
	<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.6.3.js"></script>
	<script type="text/javascript" src="placeholder.js"></script>
	<script type="text/javascript" src="fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<script type="text/javascript" src="script.js"></script>
</head>
<body>

<div id="page">
	<div id="top">
		<h1>Zwemmen in Noord-Holland</h1>
		<div id="prototype">(prototype)</div>
		<ul id="menu">
			<li><a id="informatie" class="selected" href="#">Informatie</a></li>
			<li><a id="nieuws" href="#">Nieuws</a></li>
			<li><a id="kaart" href="#">Kaart</a></li>
			<li><a id="legenda" href="#">Legenda</a></li>
			<li><a id="gezondheid" href="#">Gezondheid</a></li>
			<li><a id="wedstrijd" href="#">Wedstrijd</a></li>
		</ul>
	</div>
	<div id="content">
		<div class="contentpage" id="informatie_content">
			<div class="float left">
				<div class="giftig_2">
					<h2 style="color:red">LET OP</h2>
					<p>
						Deze site is slechts een <strong>prototype</strong> ter presentatie aan de provincie Noord-Holland.
						De data op deze site is op dit moment <strong>niet up-to-date</strong>! Voor een recent overzicht
						van risicolocaties, kijk op de <a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemwaterkwaliteit.htm" target="_blank">site van de provincie</a>.
					</p>
				</div>
				
				<p>
					Het oppervlaktewater is in Noord-Holland van goede kwaliteit. Toch is het niet aan te raden te zwemmen buiten de regelmatig gecontroleerde zwemplekken. En ook op die plekken blijft het zwemmen in oppervlaktewater altijd voor eigen risico.
					Maar over het algemeen geldt dat zwemmen op die gecontroleerde plekken goed mogelijk is. En dat is maar goed ook, in een provincie waar zo veel water lokt op hete dagen.
				</p>
				
				<p>
					Behalve tips en informatie over zaken die bij het zwemmen ergernis of zelfs gevaar kunnen opleveren, geeft deze site een overzicht van de gecontroleerde zwemplekken. Tijdens het zwemseizoen, dat duurt van 1 mei tot 1 oktober, wordt de waterkwaliteit daar tweewekelijks gecontroleerd.
				</p>
				
				<p>
					Tevens vindt u steeds de meest actuele informatie over de waterkwaliteit en andere bijzonderheden. De provincie Noord-Holland houdt u ook op de hoogte via de kabelkrant en NOS Teletekst (<strong>pagina 725</strong>).
					Verder kunt u gratis bellen naar de zwemwatertelefoon <strong itemprop="tel" class="tel">0800 9986734</strong>. U kunt via deze lijn actuele informatie krijgen over de kwaliteit en veiligheid van gecontroleerde zwemplekken. De medewerkers van de zwemwatertelefoon beschikken steeds over de meest actuele gegevens over de waterkwaliteit. Niet alleen van het oppervlaktewater, maar ook van zwembaden, sauna's, campings, hotels en kinderspeelvijvers in Noord-Holland.
				</p>
			</div>
			
			<div class="float">
				<h2>Meer informatie</h2>
				<ul>
					<li><strong>Zwemwatertelefoon</strong>: <span itemprop="tel" class="tel">0800 9986734</span> (gratis)</li>
					<li><strong>Teletekst</strong>: NOS teletekstpagina 725</li>
					<li><strong>Internet</strong>: <a href="http://www.noord-holland.nl/web/Themas/Water/Zwemwater.htm" target="_blank">Offici&euml;le website Provincie Noord-Holland</a></li>
					<li><strong>Folder</strong>: <a href="http://www.noord-holland.nl/web/file?uuid=e6b762bc-451f-4173-989d-90458cdf64ac&owner=f22bc2f4-2ebd-4086-8aa8-7e9c95211aca&contentid=809">Zwemwaterfolder 'Veilig zwemmen' (2011)</a>
				</ul>
				
				<h2>Overzicht risicolocaties</h2>
				<ul class="giftig_2">
					<?php
						foreach($locaties as $id => $locatie) {
							if ($locatie['giftig'] == 2) echo "<li><strong>{$locatie['naam']}</strong> te {$locatie['cbs']} ({$locatie['advies']})</li>";
						}
					?>
				</ul>
				<ul class="giftig_1">
					<?php
						foreach($locaties as $id => $locatie) {
							if ($locatie['giftig'] == 1) echo "<li><strong>{$locatie['naam']}</strong> te {$locatie['cbs']} ({$locatie['advies']})</li>";
						}
					?>
				</ul>
			</div>
		</div>
		
		<div class="contentpage" id="nieuws_content">
			<div class="float left">
				<p>
					Op dit moment worden nieuwsberichten enkel via de <a href="http://www.noord-holland.nl/web/Actueel/Nieuws.htm" target="_blank">offici&euml;le nieuwspagina</a> van de provincie Noord-Holland verspreid,
					een overzicht van de laatste berichten vindt u hieronder.
				</p>
				
				<ul>
					<li><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemwaterkwaliteit.htm" target="_blank">14 september 2011</a></li>
					<li><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemverbod-Oud-Valkeveen-opgeheven.htm" target="_blank">6 september 2011</a></li>
					<li><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemverbod-Oud-Valkeveen-te-Naarden.htm" target="_blank">1 september 2011</a></li>
					<li><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Westeinderplassen-Vrouwentroost-Aalsmeer-zwemverbod-opgeheven.htm" target="_blank">31 augustus 2011</a></li>
					<li><a href="http://www.noord-holland.nl/web/Actueel/Nieuws/Artikel/Zwemverbod-Westeinderplassen-Vrouwentroost-in-Aalsmeer.htm" target="_blank">26 augustus 2011</a></li>
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
			
			<div class="float">
				<p>
					Als u op de hoogte wilt worden gehouden van nieuws omtrent zwemmen en veiligheid, vul
					dan hier uw e-mail adres in en klik op <strong>Abonneer</strong>:
				</p>
				
				<div id="formulier">
					<input name="email" id="email" type="email" placeholder="E-mail adres"/>
					<button id="submit" type="submit">Abonneer</button>
					
					<br/>&nbsp;<br/><em>(nog niet functioneel)</em>
				</div>
			</div>
		</div>
		<div class="contentpage" id="kaart_content">
			<div id="map"><div id="map_canvas"></div></div>
			
			<div id="rechts">
				<div id="info">
					
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
									<h2>{$locatie['naam']}</h2>
									<div class='advies'>{$locatie['advies']}</div>
								</div>
								<div class='zwemfolder'>{$locatie['zwemfolder']}</div>
								<div class='attributen'>{$attributen}</div>
								<div class='cbs'>{$cbs_groot}</div>
							</div>
						";
					}
				?>
				</div>
			</div>
		</div>
		<div class="contentpage" id="legenda_content">
			<div class="float left">
				<p>
					Op deze site vindt u een overzicht van alle gecontroleerde openlucht zwemlocaties in de provincie
					Noord Holland. Omdat veiligheid voorop staat kunt u de kwaliteit van het water per locatie
					aflezen op de kaart.
				</p>
				
				<p class="giftig_1">
					Bij een <strong>waarschuwing</strong> (geel) is er een gering gezondheidsrisico. Vooral kwetsbare groepen (jonge kinderen, oudere mensen) wordt afgeraden te gaan zwemmen. Behoort u niet tot die groepen, dan kan zwemmen geen kwaad, zolang u geen water binnenkrijgt.
				</p>
				
				<p class="giftig_2">
					Bij een <strong>negatief zwemadvies</strong> (rood) kunt u beter niet gaan zwemmen. De kans dat u ziek wordt is dan redelijk groot.
				</p>
				
				<p class="giftig_2">
					Bij een <strong>zwemverbod</strong> (eveneens rood) is het risico dat u ziek wordt zo groot dat zwemmen voor niemand is toegestaan.
				</p>
				
				<p>
					Deze waterkwaliteit wordt in het zwemwaterseizoen (van 1 mei tot 1 oktober) elke twee weken door de provincie
					gemeten. De precieze oorzaak van het advies staat aangegeven in de lijst naast de kaart.
				</p>
				
				<p>
					De nummering en symbolen die gebruikt worden op de kaart komen overeen met de zwemwaterfolder
					die wordt uitgegeven door de provincie. In deze folder vindt u bovendien meer informatie
					over gezondheidsklachten en risico's van het buiten zwemmen.
				</p>
				
				<p>
					<a href="http://www.noord-holland.nl/web/file?uuid=e6b762bc-451f-4173-989d-90458cdf64ac&owner=f22bc2f4-2ebd-4086-8aa8-7e9c95211aca&contentid=809">Download zwemwaterfolder 'Veilig zwemmen' (2011)</a>
				</p>
			</div>
			
			<div class="float">
				<ul>
					<li><div class="attribuut doorzicht_2"></div> doorzicht < 0,5m</li>
					<li><div class="attribuut doorzicht_1"></div> doorzicht 0,5 - 1m</li>
					<li><div class="attribuut doorzicht_0"></div> doorzicht > 1m</li>
					<?php
						foreach ($attrlijst as $naam => $omschrijving) {
							echo "<li><div class='attribuut {$naam}'></div> {$omschrijving[0]}</li>";
						}
					?>
				</ul>
			</div>
		</div>
		<div class="contentpage" id="gezondheid_content">
			<p>
				Bij zwemmen in natuurwater zijn er een paar dingen waarop u moet letten. Er kunnen in het water ziektekiemen voorkomen die irritatie of gezondheidsklachten opleveren. De meest voorkomende vindt u hieronder.
				Daarnaast vindt u hier informatie over honden in zwemwater, extra risico's van zwemmen in zee, en dingen waar u over het algemeen op kunt letten.
				Voor meer informatie kijkt u op de <a href="http://www.noord-holland.nl/web/Themas/Water/Zwemwater.htm" target="_blank">website van de provincie</a>.
			</p>
			
			<div class="float left">
				<div>
					<h2>Zwemmersjeuk</h2>
					<p>
						Zwemmersjeuk is een onschuldige, maar vervelende huidirritatie, die wordt veroorzaakt door larfjes van parasieten (zuigwormen) die in het water voorkomen.
					</p>
					<p>
						Ze ontwikkelen zich in watervogels, maar kunnen ook doordringen in de huid van mensen. Daar sterven ze vanzelf, maar dat roept een afweerreactie op van de huid, die bij de een heftiger is dan bij de ander.
					</p>
					<p>
						De klachten kunnen vari&euml;ren van jeukende bultjes en zwellinkjes tot hoofdpijn en koorts. De verschijnselen kunnen heviger worden naarmate zwemmers vaker te maken hebben gehad met zwemmersjeuk.
					</p>
					<p>
						Heeft u gezwommen op een plek waar zwemmersjeuk voorkomt, douche dan goed na afloop, droogt u zich goed af en trek meteen droge kleding aan.
					</p>
				</div>
				
				<div>
					<h2>Blauwalg</h2>
					<p>
						Vooral in warme zomers kunnen in het water grote aantallen blauwalgen - ook wel cyanobacteri&euml;n genoemd - voorkomen. Ze zien eruit als een blauwgroene, olieachtige laag op het water.
					</p>
					<p>
						Blauwalgen kunnen huidirritatie veroorzaken of, wanneer u ze binnenkrijgt, maagdarmstoornissen. Zwemmen in water met een drijflaag van blauwalgen is beslist af te raden. Bent u in aanraking geweest met blauwalgen, spoelt u zich dan direct na het zwemmen goed af, ook onder uw badkleding.
					</p>
					<p>
						In tegenstelling tot blauwalgen zijn de groene algen (slierten), die ook veel voorkomen, onschuldig. Ze zijn hooguit hinderlijk tijdens het zwemmen.
					</p>
				</div>
				
				<div>
					<h2>Honden</h2>
					<p>
						Over het algemeen geldt dat uw hond in de buurt van een zwemplaats moet zijn aangelijnd. Meestal is het daar zelfs verboden terrein voor honden.
					</p>
					<p>
						Let er als hondenbezitter op waar uw huisdier zijn behoefte doet en ruim de uitwerpselen op. Informeer bij uw gemeente of er zwemplaatsen zijn waar honden wel zijn toegestaan. In de zwemwaterfolder staan deze plekken aangegeven met een symbool.
					</p>
					<p>
						Laat uw hond niet zwemmen in water waarin waterkwaliteitsproblemen zijn geconstateerd, zoals bacteriologische verontreiniging, botulisme, of blauwwieren.
					</p>
					<p>
						Ook honden lopen namelijk risico's door een duik in oppervlaktewater dat niet schoon is. Bovendien drinken honden vaak veel van het water, wat het gezondheidsrisico vergroot.
					</p>
				</div>
				
				<div>
					<h2>Let op!</h2>
					<ul>
						<li>Zwemmen in oppervlaktewater doet u altijd op eigen risico;</li>
						<li>Ga nooit alleen zwemmen;</li>
						<li>We raden u sterk af te gaan zwemmen op plekken die niet op deze kaart worden aangegeven. Ze worden niet gecontroleerd op zwemwaterkwaliteit en er kunnen stoffen of bacteri&euml;n in voorkomen die gevaarlijk zijn voor de gezondheid;</li>
						<li>Buiten een afbakening is het water vaak dieper en bestaat er bijvoorbeeld de kans op een botsing met surfers of vaartuigen;</li>
						<li>Duik niet in ondiep water of water met weinig doorzicht;</li>
						<li>Kijk goed uit voor scherpe voorwerpen in het water, draag bij voorkeur waterschoenen;</li>
						<li>Ga nooit te ver in zee, ook al kunt u goed zwemmen;</li>
						<li>Ga niet zwemmen in drijflagen van algen. Let ook op dat uw kinderen niet verstrikt raken in zulke drijflagen.</li>
					</ul>
				</div>
			</div>
			
			<div class="float">
				<div>
					<h2>Bacteri&euml;n</h2>
					<p>
						De aanwezigheid van bacteri&euml;n kan een aanwijzing zijn dat het water is besmet met ziektekiemen. De virussen of bacteri&euml;n komen in het water terecht via lozingen van rioolwater, dierlijke mest of rechtstreeks van de zwemmers.
					</p>
					<p>
						Door de virussen of bacteri&euml;n kunnen maagdarminfecties optreden. Vooral kinderen kunnen klachten krijgen als ze bacteriologisch verontreinigd water binnen krijgen. De klachten kunnen zijn maagkramp, misselijkheid, braken, koorts en diarree. Deze klachten duren over het algemeen enkele dagen tot hooguit een week.
					</p>
					<p>
						Bij warm weer kunt u uiteraard ook klachten krijgen die niet het gevolg zijn van zwemmen. Meestal komt dat dan door het eten van bedorven voedsel, onjuist bereid of bewaard voedsel of veel ijs.
					</p>
				</div>
				
				<div>
					<h2>Botulisme</h2>
					<p>
						Botulisme wordt veroorzaakt door een bacterie die bij vogels verlammingsverschijnselen teweeg brengt waaraan ze sterven.
					</p>
					<p>
						Het is belangrijk dat dode dieren zo snel mogelijk worden opgeruimd, omdat zich daarin varianten van de bacterie kunnen ontwikkelen die voor de mens schadelijk zijn.
						Raak de dode dieren beslist niet zelf aan! Waarschuw zo snel mogelijk uw gemeente. Die zorgt ervoor dat de dieren worden opgeruimd. Het kan zijn dat u dieren aantreft in water dat niet bij een bepaalde gemeente hoort (bijvoorbeeld de Noordzee of het IJsselmeer). U kunt dan contact opnemen met <a href="http://rijkswaterstaat.nl/contact" target="_blank">Rijkswaterstaat</a> (<span itemprop="tel" class="tel">0800 8002</span>).
						Vindt u dieren in officieel zwemwater, dan kunt u ook de zwemwatertelefoon bellen (<span itemprop="tel" class="tel">0800 9986734</span>, gratis).
						Wees voorzichtig met zwemmen en surfen in water waarin dode dieren liggen.
					</p>
				</div>
				
				<div>
					<h2>Zwemmen in zee</h2>
					
					<p>
						Als u in zee gaat zwemmen, ga dan niet alleen. Ga niet verder dan uw middel het water in. Let op de waarschuwingsborden en/of -vlaggen en volg de instructies van de reddingsbrigade of strandwacht op. Bij nagenoeg alle strandopgangen wordt de betekenis van de borden of vlaggen aangegeven.
					</p>
					
					Enkele voorbeelden van mogelijke gevaren:
					<ul>
					<li>
						<strong>Sterke wind vanaf het land (oostenwind)</strong><br/>
						Denk eraan dat opblaasboten en luchtbedden snel worden meegenomen door de zee. Als u gaat zwemmen, besef dan dat het makkelijk is de zee in te zwemmen, maar dat u moeilijk tegen de wind en stroming in terug kunt komen naar het strand.
					</li>
					<li>
						<strong>Onderkoeling</strong><br/>
						Als u lang in het koude zeewater blijft, kunt u onderkoeld raken. Bij (lichte) onderkoeling daalt de lichaamstemperatuur en gaat u klappertanden en rillen. Uw hart gaat sneller slaan. U kunt dan beter gaan opwarmen op het strand. Blijft u te lang in zee, dan wordt uw denkvermogen minder, spieren verstijven, de hartslag zakt. Kou en pijn verdwijnen en u wordt onverschillig. De gevolgen laten zich raden.
					</li>
					<li>
						<strong>Kramp</strong><br/>
						Omdat u snel afkoelt in zeewater, kunt u worden overvallen door spierkramp. U kunt het beste rustig het water ingaan. Blijf bij kramp zo kalm mogelijk en probeer uw spieren te ontspannen.
					</li>
					<li>
						<strong>Muien</strong><br/>
						Muien zijn geulen die min of meer loodrecht op het strand staan. Ze worden gevormd door water dat tussen twee banken door terugstroomt in zee. Ook langs strekdammen komt zo'n stroming vaak voor. Zwemmen dicht langs zulke dammen is altijd af te raden.
						Afhankelijk van de hoogte van de golven en de windrichting kunnen muien op andere plekken ook gevaar opleveren, vooral voor kinderen. Let daarom goed op de aanwijzingen die op bewaakte stranden worden gegeven.
					</li>
				</div>
			</div>
		</div>
		<div class="contentpage" id="wedstrijd_content">
			<p>
				Deze site is een prototype gemaakt voor de wedstrijd <a href="http://www.appsfornoordholland.nl/" target="_blank">Apps for Noord-Holland</a>. We willen laten zien hoeveel voordelen een online platform voor de informatieverstrekking omtrent veilig zwemmen biedt.
				We doelen hiermee zeker niet alleen op Noord-Holland, een uiteindelijke oplossing voor heel Nederland zou natuurlijk het beste zijn.
			</p>
			
			<div class="float left">
				<h2>Visie</h2>
				<p>
					Een website als deze kan als tussenstap dienen voor integrale informatievoorziening. Net als met teletekst zouden alle provincies de data omtrent zwemlocaties via importeerscripts kunnen laden in de centrale database.
					Op deze manier kan zelfs &eacute;&eacute;n live-API beschikbaar worden gesteld die werkt voor heel Nederland, omdat de verschillen van dataverwerking per provincie kunnen worden gladgestreken bij het importeerproces.
					Ook nieuwsberichten, gezondheidinformatie, en andere vormen van communicatie kunnen door de verschillende provincies bijeen worden gebracht, wat consistentie in de presentatie oplevert, en kosten kan besparen doordat teksten onder vrije licenties kunnen worden hergebruikt.
					Zoals we op deze site ook al laten zien, kunnen gebruikers direct meldingen krijgen van updates vanuit provincies door middel van het achter laten van een e-mail adres.
				</p>
				
				<h2>Toerisme</h2>
				<p>
					Voor gebruikers is het prettig als er &eacute;&eacute;n punt is op het web waar men terecht kan bij het inwinnen van informatie over veiligheid, maar ook om zwemlocaties in eigen omgeving of op toeristische uitstap te vinden.
					Dit is dan ook een uitgelezen kans om integraal deze informatie in verschillende talen aan te bieden, omdat dit minder zou kosten dan wanneer elke provincie dit zelf moet doen. Voor heel Nederland zou dit dan ook een prachtige kans zijn
					om een goede indruk achter te laten bij internationale toeristen.
				</p>
				
				<h2>Crowdsourcing</h2>
				<p>
					Een van de toepassingen van een nationaal webplatform als deze is de terugkoppeling van gebruikers. In dit prototype laten we al zien dat het tonen van goede afbeeldingen bij de locaties kan worden uitbesteed aan gebruikers, door hen te laten stemmen op goede foto's.
					Maar het is bijvoorbeeld ook mogelijk dat bezoekers van zwemlocaties zelf kunnen aangeven als ze iets verdachts gezien hebben. Nog een stap verder is dat mensen zelf foto's en berichten
					kunnen achterlaten om zo een conversatie te beginnen, zo kunnen tips en ervaringen uitgewisseld worden.
				</p>
			</div>
			
			<div class="float">
				<h2>Native apps</h2>
				<p>
					Wanneer een integrale API beschikbaar is waar alle provincies op kunnen aansluiten, kunnen de apps voor smartphones die al gemaakt zijn voor de Apps for Noord-Holland wedstrijd hier eenvoudig voor worden aangepast. De beschikking hebben
					tot recente informatie via mobiele telefoons zou de zwemveiligheid kunnen verbeteren. Maar dat niet alleen: de crowdsource toepassingen strekken zich nog verder uit naar het mobiele platform. Zo zouden gebruikers onmiddellijk
					foto's kunnen uploaden, bijvoorbeeld om een sfeerimpressie te geven, maar ook om een verdachte situatie te melden. Daarnaast is het mogelijk via push-berichten gebruikers direct in te lichten wanneer er waarschuwingen worden uitgegeven voor zwemlocaties
					in hun provincie of zelfs in een bepaalde straal rondom een opgegeven postcode. Tenslotte is het mogelijk voor gebruikers om snel te zien of er toevallig vrienden in de buurt zijn van een zwemlocatie door middel van koppeling met sites zoals Foursquare.
				</p>
				
				<h2>Legio mogelijkheden</h2>
				<p>
					Met deze site willen wij een voorproefje geven van wat vandaag de dag mogelijk is om met informatievoorziening zowel veiligheid van zwemmen als toerisme in het algemeen te bevorderen is. Aan het eind van de wedstrijd Apps for Noord-Holland geven wij de broncode van deze site
					vrij voor eenieder om te bestuderen, met name gericht op de provincie Noord-Holland, andere provincies, en Rijkswaterstaat. Wij hopen dan ook dat dit uiteindelijk impuls kan geven aan een nationaal besluit tot een integrale online informatievoorziening omtrent zwemveiligheid.
				</p>
			</div>
		</div>
	</div>
</div>

</body>
</html>
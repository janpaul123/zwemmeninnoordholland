var markers = new Array();
var center = new google.maps.LatLng(52.667063, 4.886169);
var indebuurt;
var map = null;

function maakKnop(html, title) {
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
	controlText.innerHTML = html;
	controlUI.appendChild(controlText);
	
	return controlDiv;
}

function updateFixedLocation() {
	var left = $('#content').offset().left;
	$('#top').css('left', left);
	$('#map').css('left', left);
}

function selectLocatie($locatie) {
	$('.locatie.selected').removeClass('selected');
	$locatie.addClass('selected');
	var id = $locatie.attr('id');
	if ($locatie.find('.photos').size() <= 0) {
		$photos = $('<div class="photos"></div>');
		
		for (var i in locaties) {
			if (locaties[i][0] == id) {
				for (var j in locaties[i][5]) {
					var photoArray = locaties[i][5][j];
					$img = $('<img></img>');
					$img.attr('src', 'http://mw2.google.com/mw-panoramio/photos/mini_square/' + photoArray[0] + '.jpg');
					$a = $('<a></a>');
					$a.attr('href', 'http://mw2.google.com/mw-panoramio/photos/medium/' + photoArray[0] + '.jpg');
					$a.attr('rel', 'photos_' + id);
					$a.append($img);
					$photos.append($a);
				}
				
				$photos.find('a').fancybox({
						'overlayShow': true,
						'overlayOpacity': 0,
						'overlayColor' : '#fff',
						'transitionIn'	: 'none',
						'transitionOut'	: 'none',
						'titleFormat' : function(title, currentArray, currentIndex, currentOpts) {
							return '<span id="fancybox-title-over">' +
								'<a href="http://www.panoramio.com/photo/' + locaties[i][5][currentIndex][0] + '" target="_blank"><img src="http://www.panoramio.com/img/logo-small.gif"/></a>' +
								'<div class="auteur">Auteur: <a href="http://www.panoramio.com/user/' + locaties[i][5][currentIndex][1] + '" target="_blank">' + locaties[i][5][currentIndex][2] + '</a></div>' +
								'<div class="copyright">Photos provided by Panoramio are under the copyright of their owners.</span></span>';
						},
						'onComplete' : function() {
							$('.duim').remove();
							$('#fancybox-outer').append('<div id="omhoog" class="duim" title="Omhoog stemmen"></div><div id="omlaag" class="duim" title="Omlaag stemmen"></div>');
							$('.duim').click(function() { $('.duim').removeClass('clicked'); $(this).addClass('clicked'); });
						}
					});
				
				break;
			}
		}
		
		$locatie.find('.binnenin').append($photos);
	}
}

function initMap() {
	map = new google.maps.Map(document.getElementById("map_canvas"), {
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
	
	/*
	var panoramioLayer = new google.maps.panoramio.PanoramioLayer();
	google.maps.event.addListener(panoramioLayer, 'click', function(event) {
		var photoDiv = document.getElementById('photoPanel');
		var attribution = document.createTextNode(event.featureDetails.title + ": " + event.featureDetails.author);
		var br = document.createElement("br");
		var link = document.createElement("a");
		link.setAttribute("href", event.featureDetails.url);
		link.appendChild(attribution);
		photoDiv.appendChild(br);
		photoDiv.appendChild(link);
	});
	
	var fotoknop = maakKnop('<img src="camera.png"/>', "Toon foto's");
	fotoknop.index = 3;
	map.controls[google.maps.ControlPosition.TOP_RIGHT].push(fotoknop);
	
	google.maps.event.addDomListener(fotoknop, 'click', function() {
		if (panoramioLayer.getMap() == null) {
			panoramioLayer.setMap(map);
		}
		else {
			panoramioLayer.setMap(null);
		}
	});
	*/
	
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
			selectLocatie($id);
		});
		markers[locatie[0]] = marker;
		
		$('#' + locatie[0]).click(function() {
			var marker = markers[$(this).attr('id')];
			if (map.getZoom() < 12) map.setZoom(12);
			map.panTo(marker.getPosition());
			selectLocatie($(this));
		});
	}
	
	updateFixedLocation();
}

$(document).ready(function() {
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
		$('#' + $(this).attr('id') + '_content').show();
		$('#menu a').removeClass('selected');
		$(this).addClass('selected');
		e.preventDefault();
	});
	
	$('#kaart').click(function(e) {
		if (map == null) initMap();
	});
	
	
	$(window).resize(updateFixedLocation);
});

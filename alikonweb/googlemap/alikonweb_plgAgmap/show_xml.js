		var map;
		var file;
		var markers = [];
		var infoWindow;
		var locationSelect;

		function initialize(myfile,mymap,lat,lon,zoom) {
		    map=mymap;
			file=myfile;
			map = new google.maps.Map(document.getElementById(map), {
                  center: new google.maps.LatLng(lat, lon),
                    zoom: zoom,
               mapTypeId: 'roadmap',
   mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
			});
			infoWindow = new google.maps.InfoWindow();

			locationSelect = document.getElementById("locationSelect");
			locationSelect.onchange = function() {
				var markerNum = locationSelect.options[locationSelect.selectedIndex].value;
				if (markerNum != "none"){
					google.maps.event.trigger(markers[markerNum], 'click');
				}
			};
			showLocations(file);
		}

		function showLocations(file) {
			clearLocations();
			downloadUrl(file, function(data) {
				var xml = parseXml(data);
				var markerNodes = xml.documentElement.getElementsByTagName("marker");
				var bounds = new google.maps.LatLngBounds();
				for (var i = 0;
				i < markerNodes.length;
				i++) {
					var name = markerNodes[i].getAttribute("username");
					var title = markerNodes[i].getAttribute("html");
					var city = markerNodes[i].getAttribute("city");
					var latlng = new google.maps.LatLng(
					parseFloat(markerNodes[i].getAttribute("lat")),
					parseFloat(markerNodes[i].getAttribute("lng")));
                                         
                                        if (file.search('contentmap.xml')>-1){
                                             mymarker ="<a href='" + name + "'>"+title+"</a> <br/>" + city;
                                             myoption =title + "(" + city + ")";
                                        }else{
                                             mymarker ="<b>" + name + "</b> <br/>" + city;
                                             myoption =name + " (" + title + ") "+ city;
                                        }
                                       createOption(myoption, i);
					//createOption(name, city, title, i);
					//createMarker(latlng, name, city, title);
                                        createMarker(latlng, mymarker);
                                        
					bounds.extend(latlng);
				}
				map.fitBounds(bounds);
				locationSelect.style.visibility = "visible";
				locationSelect.onchange = function() {
					var markerNum = locationSelect.options[locationSelect.selectedIndex].value;
					google.maps.event.trigger(markers[markerNum], 'click');
				};
			});
		}
		function clearLocations() {
			infoWindow.close();
			for (var i = 0;
			i < markers.length;
			i++) {
				markers[i].setMap(null);
			}
			markers.length = 0;
			locationSelect.innerHTML = "";
			var option = document.createElement("option");
			option.value = "none";
			option.innerHTML = "See all results:";
			locationSelect.appendChild(option);
		}
		function createOption(myoption, num) {
			var option = document.createElement("option");
			option.value = num;
			option.innerHTML = myoption;
			locationSelect.appendChild(option);
		}
		function createMarker(latlng, mymarker) {
			//var html = "<b>" + name + "</b> <br/>" + city;
                        //var html = "<a href='" + name + "'>"+title+"</a> <br/>" + city;
                        var html=mymarker;
			var marker = new google.maps.Marker({
map: map,
position: latlng
			});
			google.maps.event.addListener(marker, 'click', function() {
				infoWindow.setContent(html);
				infoWindow.open(map, marker);
			});
			markers.push(marker);
		}
		function downloadUrl(url, callback) {
			var request = window.ActiveXObject ?
			new ActiveXObject('Microsoft.XMLHTTP') :
			new XMLHttpRequest;

			request.onreadystatechange = function() {
				if (request.readyState == 4) {
					request.onreadystatechange = doNothing;
					callback(request.responseText, request.status);
				}
			};

			request.open('GET', url, true);
			request.send(null);
		}

		function parseXml(str) {
			if (window.ActiveXObject) {
				var doc = new ActiveXObject('Microsoft.XMLDOM');
				doc.loadXML(str);
				return doc;
			} else if (window.DOMParser) {
				return (new DOMParser).parseFromString(str, 'text/xml');
			}
		}

		function doNothing() {}
		
		
<?php

/**
 * @package	Alikonweb Applications 4 Joomla
 * @subpackage	Alikonweb Googlemap Plugin
 * @license		GNU General Public License version 3; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgAlikonwebGooglemap extends JPlugin {

    protected $is_online = null;

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
        $this->loadLanguage('plg_alikonweb_googlemap.sys', JPATH_ADMINISTRATOR);
        $this->is_connected();
    }

    function rnd_mapname() {
        $aa = '';
        for ($i = 0; $i < 6; $i++) {
            $d = rand(1, 30) % 2;
            $aa.=chr(rand(65, 90));
        }
        return $aa;
    }

    function onXml($mode, $filexml, $indirizzo, $zoomlevel, $width, $eight, $lat, $lon) {
        if (!$this->is_online) {
            $url = 'Not connected';
            return $url;
        };
        $code = '';
        $mapName = $this->rnd_mapname();
        $msg1 = 'No results found';
        $msg2 = 'Geocoder failed due to:';
        $filexml = juri::base() . $filexml;
        //
        $document = & JFactory::getDocument();
       // $document->addCustomTag('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=weather"></script>');
        $document->addCustomTag('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>');
        $document->setMetaData('viewport', 'initial-scale=1.0, user-scalable=no');
        $link_url = JURI::root() . 'plugins/alikonweb/googlemap/alikonweb_plgAgmap/';
        JHTML::script('show_xml.js', $link_url, true);
        $url = '<!-- inizio alikonweb GoogleMaps v.3 plugin for joomla -->';
        $js = "window.addEvent('load', function(){ initialize('" . $filexml . "','" . $mapName . "','" . $lat . "','" . $lon . "') })";
        $document->addScriptDeclaration($js);
        //





        $url = '<br /><!-- inizio alikonweb GoogleMaps v.3 plugin for joomla --><br />';
        $url.= '<div id="' . $mapName . '" style="margin:5px 20px 5px 5px; float: left;  width: ' . $width . 'px; height: ' . $eight . 'px"></div>';
        $url.= '<div><select id="locationSelect" style="width:' . $width . 'px;visibility:hidden"></select></div>';
        $url.= '<br /><!-- fine alikonweb GoogleMaps plugin for joomla --><br />';
        return $url;
    }

    function onShowMap($mode, $testo, $indirizzo, $zoomlevel, $width, $eight, $lat, $long) {
        if (!$this->is_online) {
            $url = 'Not connected';
            return $url;
        };
        $code = '';


        $msg1 = 'No results found';
        $msg2 = 'Geocoder failed due to:';
        $document = & JFactory::getDocument();
        $document->setMetaData('viewport', 'initial-scale=1.0, user-scalable=no');

        //$js = "http://maps.google.com/maps/api/js?sensor=false";
        //$document->addScript($js);

        $url = '<br /><!-- inizio alikonweb GoogleMaps v.3 plugin for joomla --><br />';
        $zoom = $zoomlevel;
        $mapName = $this->rnd_mapname();
        $mapType = 'ROADMAP';
        $js = "http://maps.google.com/maps/api/js?sensor=false&libraries=weather";

        $document->addScript($js);
        $mapOptions = '';
        $markerOptions = '';
        /*
          if(true){
          $title = 'titolo';

          $markerOptions =<<<EOL

          var opts = new Object;
          opts.title = "{$title}";
          opts.position = {$mapName}.getCenter();
          opts.map = $mapName;
          marker = new google.maps.Marker(opts);
          EOL;
          }
         */
        $navControls = true;
        /*
          if($params->get('navControls', false) == 0){
          $mapOptions .= ',disableDefaultUI: false'. PHP_EOL;
          $navControls = false;
          }

          if($params->get('smallmap')){
          $mapOptions .=  ', navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL} ' . PHP_EOL;
          $navControls = true;
          }
         */
        if (!$navControls)
            $mapOptions .= ',navigationControl: false' . PHP_EOL;

        /* 	
          if($params->get('static')){
          $mapOptions .= 	', draggable: false' .PHP_EOL;
          }
         */
        $mapTypeControl = 'true';

        $mapOptions .= ",mapTypeControl: {$mapTypeControl}" . PHP_EOL;
        $mapOptions .= ",streetViewControl: true" . PHP_EOL;
        $script = <<<EOL
	google.maps.event.addDomListener(window, 'load', {$mapName}load);
	 var {$mapName};
	 var geocoder;
    function {$mapName}load() {
    geocoder = new google.maps.Geocoder();	
		var options = {
			zoom : {$zoomlevel},
			center: new google.maps.LatLng({$lat}, {$long}),
			mapTypeId: google.maps.MapTypeId.{$mapType}
			{$mapOptions}
		}
		
       {$mapName} = new google.maps.Map(document.getElementById("{$mapName}"), options);
		{$markerOptions}	
                var weatherLayer = new google.maps.weather.WeatherLayer({
                    temperatureUnits: google.maps.weather.TemperatureUnit.CELSIUS
                });
                weatherLayer.setMap({$mapName});
                var trafficLayer = new google.maps.TrafficLayer();
                trafficLayer.setMap({$mapName});
		{$mapName}codeAddress();	
    }
     
     
    function {$mapName}codeAddress() {
    var address = document.getElementById("{$mapName}address").value;
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        {$mapName}.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: {$mapName}, 
            position: results[0].geometry.location
        });
		 var nome=document.getElementById("{$mapName}nomecontatto").value
		 var infoWindow = new google.maps.InfoWindow(
		 
                { content: '<b>'+nome+'</b><br>'+results[0].formatted_address,
                  size: new google.maps.Size(250,250),
				  position:{$mapName}.getCenter()
		 /*		
                 { content: '<b>'+nome+'</b>&nbsp;'+results[0].geometry.location,
                  size: new google.maps.Size(250,250),
				  position:{$mapName}.getCenter()
                 */                 

         });
		 infoWindow.open({$mapName});
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  } 
EOL;

        JHTML::_('behavior.mootools');

        $document->addScriptDeclaration($script);
//----------------------------------------//

        $url.='<div id="' . $mapName . '" style="float:left; width: ' . $width . 'px; height: ' . $eight . 'px"></div>';
        $url.='<div style="float:clear;">
    <input id="' . $mapName . 'address" type="hidden" value="' . $indirizzo . '">
    <input id="zoomlevel" type="hidden" value="' . $zoomlevel . '" >
	<input id="' . $mapName . 'nomecontatto" type="hidden" value="' . $testo . '" >
  </div>';
        $url.='<br /><!-- fine alikonweb GoogleMaps plugin for joomla --><br />';
        return $url;
    }

    function onShowDirections($mode, $testo, $indirizzo, $zoomlevel, $width, $eight, $lat, $lon, $base) {
        if (!$this->is_online) {
            $url = 'Not connected';
            return $url;
        };
// Get plugin info
        $plugin = & JPluginHelper::getPlugin('alikonweb', 'googlemap');
        $params = new JRegistry;
//$pluginParams = new JParameter( $plugin->params );
        if (!$base) {
//$base=$pluginParams->get( 'base', 'bari' );
            $base = $params->get('base', 'bari');
        }
        $code = '';
        $mappa = 'acmap';
        $msg1 = 'No results found';
        $msg2 = 'Geocoder failed due to:';
        $document = & JFactory::getDocument();
        $document->addCustomTag('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=weather"></script>');
        $document->setMetaData('viewport', 'initial-scale=1.0, user-scalable=no');
        $link_url = JURI::base() . 'plugins/alikonweb/googlemap/alikonweb_plgAgmap/';
        JHTML::script('show_directions2.js', $link_url, true);
        $url = '<!-- inizio alikonweb GoogleMaps v.3 plugin for joomla -->';
//$js = "window.addEvent('load', function(){ initialize('".$indirizzo."','".$mappa."',".$zoomlevel."); })";
        $js = "window.addEvent('load', function(){ initialize() })";
        $document->addScriptDeclaration($js);
//$url.='<div><form action="#">';
        $url.='<div>';
        $url.='Start from:<input id="partenza" type="textbox" name="partenza" value="' . $base . '">';
        $url.='<input id="arrivo" type="hidden" value="' . $indirizzo . '">';
        $url.='<input type="button" value="Directions" onclick="calcRoute2()">';

        $url.='</div>';
        $url.='<div>';
        $url.='<div id="' . $mappa . '" style=" float:left; width: ' . $width . 'px; height: ' . $eight . 'px"></div>';
        $url.='<div id="directionsPanel" style="width: ' . $width . 'px; height: ' . $eight . 'px; overflow:auto;"></div>';
        $url.='</div>';
        $url.='<!-- fine alikonweb GoogleMaps plugin for joomla -->';
        return $url;
    }

    function onDistance($lat, $lon, $distanza) {
// $lat = '37';
// $lon = '-122';
        $path = $this->rnd_mapname() . "DISusermap.xml";

        $db = JFactory::getDbo();
//$query = $db->getQuery(true);
        $query = 'SELECT a.username, e.longitude, e.countryname, e.citta, e.stato, e.latitude, e.acamp1,';
        $query.='( 6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians(' . $lon . ') ) + sin( radians(' . $lat . ') ) * sin( radians( e.latitude ) ) ) ) AS distance ';
        $query.=' FROM #__users AS a, #__userextras AS e';
        $query.=' WHERE a.id=e.id ';
        $query.=' HAVING distance < ' . $distanza . ' ORDER BY distance';
        //  echo $query;
        $db->setQuery($query);
        $result = $db->loadObjectList();
        $myxml = "<markers>\n";
        $cont = 0;
        $coord = array();
        foreach ($result as $row) {
            $cont++;
            $myxml .= "<marker 		          
                           name='" . $this->parseToXML($row->username) . "'    
			   address='" . $this->parseToXML($row->citta) . "'						   
			   lat='" . $row->latitude . "'
                           lng='" . $row->longitude . "'
			   distance='" . $row->distance . "'>
		        </marker>";
            if ($cont == 1) {
                $coord[] = $row->latitude;
                $coord[] = $row->longitude;
            }
        }
        $myxml .= "</markers>\n";
        $filenum = fopen($path, "w");
        $path = juri::base() . $path;

        fwrite($filenum, $myxml);
        fclose($filenum);
        return $path;
    }

    function parseToXML($htmlStr) {
        $xmlStr = str_replace('<', '&lt;', $htmlStr);
        $xmlStr = str_replace('>', '&gt;', $xmlStr);
        $xmlStr = str_replace('"', '&quot;', $xmlStr);
        $xmlStr = str_replace("'", '&#39;', $xmlStr);
        $xmlStr = str_replace("&", '&amp;', $xmlStr);
        return $xmlStr;
    }

    function onNation($stato) {

        $path = $stato . "usermap.xml";
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('a.username, e.longitude, e.countryname, e.citta, e.stato, e.latitude');
//$query->order('a.registerDate DESC');
        $query->from('#__users AS a, #__userextras AS e');
        $query->where('a.id=e.id and e.stato="' . $stato . '"');
//	$query->where('a.id=e.id');
        $query->order('a.registerDate ASC');
        $db->setQuery($query);
        $result = $db->loadObjectList();
        $myxml = "<markers>\n";
        $cont = 0;
        $coord = array();
        foreach ($result as $row) {
            $cont++;
            $myxml .= "<marker 
		           html='" . $this->parseToXML($row->countryname) . "' 
						   city='" . $this->parseToXML($row->citta) . "'
						   username='" . $this->parseToXML($row->username) . "'
						   lat='" . $row->latitude . "'
						   lng='" . $row->longitude . "'
						   type='user'>
		           </marker>";
            if ($cont == 1) {
                $coord[] = $row->latitude;
                $coord[] = $row->longitude;
            }
        }
        $myxml .= "</markers>\n";
        $filenum = fopen($path, "w");
        $url = JURI::base() . $stato . "usermap.xml";
        fwrite($filenum, $myxml);
        fclose($filenum);
        return (array) $coord;
    }

    function onShowDistance($testo, $width, $eight) {
// Get plugin info

        $plugin = & JPluginHelper::getPlugin('alikonweb', 'googlemap');
        $params = new JRegistry;
        //$filexml = juri::base() . $testo;
        $juri_root = JURI::root() . 'index.php';
        $document = & JFactory::getDocument();
//----/
        $script = <<<EOL

var map;
var markers = [];
var infoWindow;
var locationSelect;

function initialize() {
map = new google.maps.Map(document.getElementById("dmap"), {
center: new google.maps.LatLng(47, 12),
 zoom: 4,
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
//startsearchLocations();
}

function searchLocations() {
var address = document.getElementById("addressInput").value;
var geocoder = new google.maps.Geocoder();
geocoder.geocode({address: address}, function(results, status) {
if (status == google.maps.GeocoderStatus.OK) {
//alert(results[0].geometry.location);
alert ('pippo');
//searchLocationsNear(results[0].geometry.location);
} else {
alert(address + ' not found');
}
});
}

function startsearchLocations() {
var address = 'Mountain view';
var geocoder = new google.maps.Geocoder();
geocoder.geocode({address: address}, function(results, status) {
if (status == google.maps.GeocoderStatus.OK) {
alert(results[0].geometry.location);
searchLocationsNear(results[0].geometry.location);
} else {
alert(address + ' not found');
}
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

function searchLocationsNear(center, file) {
clearLocations();

var radius = document.getElementById('radiusSelect').value;
 
downloadUrl(file, function(data) {
var xml = parseXml(data);
var markerNodes = xml.documentElement.getElementsByTagName("marker");
var bounds = new google.maps.LatLngBounds();
for (var i = 0;
i < markerNodes.length;
i++) {
var name = markerNodes[i].getAttribute("name");
var address = markerNodes[i].getAttribute("address");
var distance = parseFloat(markerNodes[i].getAttribute("distance"));
var latlng = new google.maps.LatLng(
parseFloat(markerNodes[i].getAttribute("lat")),
 parseFloat(markerNodes[i].getAttribute("lng")));

createOption(name, distance, i);
createMarker(latlng, name, address);
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

function createMarker(latlng, name, address) {
var html = "<b>" + name + "</b> <br/>" + address;
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

function createOption(name, distance, num) {
var option = document.createElement("option");
option.value = num;
option.innerHTML = name + "(" + distance.toFixed(1) + ")";
locationSelect.appendChild(option);
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

function calcxml(punto){

var address = document.getElementById("addressInput").value;
//var file=null;
var box= document.id('consolebox');
var radius = document.getElementById('radiusSelect').value;

box.style.display="block";
box.set('html', 'Search in progress...');
var url="{$juri_root}?option=com_aa4j&amp;format=raw&amp;task=doxml&amp;lat="+punto.lat()+"&amp;lon="+punto.lng()+"&amp;dis="+radius;
var a=new Request.JSON({
url:url,
onFailure: function(){  
        alert('oh nooo!');  
    },  
 onComplete: function(path){
searchLocationsNear(punto, path);
box.set('html', 'loaded');
var el = $(box);
(function(){
el.set('html', '');
}).delay(1500);
}
});
a.get();
}

function searchLocationsajax(){
var address = document.getElementById("addressInput").value;
var geocoder = new google.maps.Geocoder();
geocoder.geocode({address: address}, function(results, status) {
if (status == google.maps.GeocoderStatus.OK) {

calcxml (results[0].geometry.location);
 //alert(results[0].geometry.location);


} else {
alert(address + ' not found');
}
});
}
EOL;

        $document->addScriptDeclaration($script);
        $document->addCustomTag('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>');
        $document->setMetaData('viewport', 'initial-scale=1.0, user-scalable=no');



//$link_url = JURI::base() . 'plugins/alikonweb/googlemap/alikonweb_plgAgmap/';
//JHTML::script('show_distance.js', $link_url, true);
        $url = '<!-- inizio alikonweb GoogleMaps v.3 plugin for joomla -->';

        $js = "window.addEvent('load', function(){ initialize() })";
        $document->addScriptDeclaration($js);

        $url.='<div>';
        $url.='<input type="text" id="addressInput" size="40"/>';
        $url.='<select id="radiusSelect">';
        $url.='<option value="5" selected> 5 km</option>';
        $url.='<option value="25" selected>25 km</option>';
        $url.='<option value="50" selected>50 km</option>';
        $url.='<option value="100">100 km</option>';
        $url.='<option value="200">200 km</option>';
        $url.='</select>';
        $url.='<input type="button" onclick="searchLocationsajax()" value="Search"/><span id="consolebox"></span>';
        $url.='</div>';
        $url.='<div>';
        $url.='<div id="dmap" style="width:' . $width . 'px; height:' . $eight . 'px;"></div>';

        $url.='<select id="locationSelect" style="width:' . $width . 'px;visibility:hidden"></select>';
        $url.='</div>';

        $url.='<!-- fine alikonweb GoogleMaps plugin for joomla -->';
        return $url;
    }

    private function is_connected() {
//check to see if the local machine is connected to the web 
//uses sockets to open a connection to google.com 
        $connected = @fsockopen("www.google.com", 80);
        if ($connected) {
            $this->is_online = true;
            fclose($connected);
        } else {
            $this->is_online = false;
        }
        return true;
    }

    function onGeocode($latitude, $longitude, $acamp1, $citta, $countryname, $stato, $username) {

        $document = & JFactory::getDocument();
        $document->setMetaData('viewport', 'initial-scale=1.0, user-scalable=no');

        $js = "http://maps.googleapis.com/maps/api/js?sensor=false&language=en";

        $document->addScript($js);
        $user_lat = $latitude;
        $user_lon = $longitude;
        $user_add = $acamp1;
        $link_url = JURI::root() . 'plugins/alikonweb/googlemap/alikonweb_plgAgmap/';
        JHTML::script('show_articles.js', $link_url, true);
      //  jexit(mysql_escape_string($acamp1));
        JHTML::_('behavior.mootools');
        //$document->addScriptDeclaration($script);
        $js = "window.addEvent('load', function(){ iniz(".$latitude.", ".$longitude.",'".mysql_escape_string($acamp1)."','".$citta."','".$countryname."','".$stato."','".$username."') })";
        $document->addScriptDeclaration($js);



        echo '<div id="map" style="float:left; width:380px; height:400px;"></div>';
        echo '<div>';
        echo '<input type="text" id="addressInput" size="40"/>';
        echo '<input type="button" onclick="searchAddress()" value="Search"/>';
        echo '</div>';
      //  echo '<input type="button" onclick="saveLocationsajax()" value="Save Coord"/><span id="consolebox"></span></div>';
    }

}


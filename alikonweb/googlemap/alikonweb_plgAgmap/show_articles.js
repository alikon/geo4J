
      var map; 
      var infowindow; 
      var geocoder; 
      function iniz(lat,lon,username,acamp1,citta,stato,countryname) { 
        map = new google.maps.Map(document.getElementById('map'), { 
          zoom:4, 
          center: new google.maps.LatLng(lat, lon), 
          mapTypeId: google.maps.MapTypeId.ROADMAP 
        }); 
         
        infoWindow = new google.maps.InfoWindow(); 

        google.maps.event.addListener(map, 'click', findAddress); 
        contentString=username+' <br> '+acamp1; 
         var infowindow = new google.maps.InfoWindow({ 
            content: contentString 
        }); 
      var marker = new google.maps.Marker({ 
            position: map.center, 
            map: map, 
            title: username 
        }); 
      google.maps.event.addListener(marker, 'click', function() { 
          infowindow.open(map,marker); 
        }); 
        document.getElementById('jform_metadata_lat').innerHTML= lat;    
        document.getElementById('jform_metadata_lng').innerHTML= lon;    
        document.getElementById('jform_metadata_citta').innerHTML= citta;    
        document.getElementById('jform_metadata_stato').innerHTML= countryname;    
        document.getElementById('jform_metadata_siglastato').innerHTML= stato;    
        document.getElementById('jform_metadata_formattedaddress').innerHTML=acamp1;    
      } 

      function findAddress(event) { 
        
    var mylat=document.getElementById("jform_metadata_lat"); 
    var mylon=document.getElementById("jform_metadata_lng"); 
    var formattedaddress=document.getElementById("jform_metadata_formattedaddress");         
    var geocoder = new google.maps.Geocoder(); 
    geocoder.geocode({latLng: event.latLng}, function(results, status) { 
        if (status == google.maps.GeocoderStatus.OK) { 
            if (results[0]) { 
                 
                                formattedaddress.innerHTML=results[0].formatted_address; 
                infoWindow.setContent(results[0].formatted_address); 
                mylat.innerHTML=event.latLng.lat(); 
                mylon.innerHTML=event.latLng.lng(); 
                infoWindow.setPosition(results[0].geometry.location); 
                infoWindow.open(map); 
            } 
        } 
    }); 
    codeLatLng(event.latLng.lat(), event.latLng.lng()); 
      } 
      function codeLatLng(lat, lng) { 
    var stato=document.getElementById("jform_metadata_stato"); 
        
    var siglastato=document.getElementById("jform_metadata_siglastato"); 
    var formattedaddress=document.getElementById("jform_metadata_formattedaddress"); 
    //var geometry=document.getElementById("jform_metadata_geometry"); 
    var postalcode=document.getElementById("jform_metadata_postalcode"); 
    var citta=document.getElementById("jform_metadata_citta"); 
    var latlng = new google.maps.LatLng(lat, lng); 
        var geocoder = new google.maps.Geocoder(); //qui 
    geocoder.geocode({'latLng': latlng}, function(results, status) { 
        if (status == google.maps.GeocoderStatus.OK) { 
             
            formattedaddress.value=results[0].formatted_address; 
    //        geometry.innerHTML=results[0].geometry.location; 
            if (results[0]) { 
                 
                    var address = results[0].address_components; 
                                formattedaddress.innerHTML=results[0].formatted_address; 
        
                                risp=""; 
                                risp=cerca_postal_code(results); 
                postalcode.value=risp; 
                risp=""; 
                risp=cerca_locality(results);          
                citta.value=risp; 
                state=cerca_stato(results); 
                stato.value=state[0]; 
                siglastato.value=state[1]; 
                                document.getElementById('jform_metadata_lat').value= lat;    
                                document.getElementById('jform_metadata_lng').value= lng;    
   

            } else { 
                alert("No results found"); 
            } 
            //} 
        } else { 
            alert("Geocoder failed due to: " + status); 
        } 
    });    
      }       
      function cerca_postal_code(results){ 

    var i, j, 
    result, types; 
        postcode=""; 

    // Loop through the Geocoder result set. Note that the results 
    // array will change as this loop can self iterate. 
    for (i = 0; i < results.length; i++) { 

        result = results[i]; 

        types = result.types; 

        for (j = 0; j < types.length; j++) { 

            if (types[j] === 'postal_code') { 

                // If we haven't found the "long_name" property, 
                // then we need to take this object and iterate through 
                // it again by setting it to our master loops array and  
                // setting the index to -1 
                if (result.long_name === undefined) { 
                    results = result.address_components; 
                    i = -1; 
                } 
                // We've found it! 
                else { 
                    postcode = result.long_name; 
                } 

                break; 

            } 

        } 
         
    }                 
    return postcode;     
} 
function cerca_stato(results){ 

    var i, j, 
    result, types; 
        console.log(results); 
        var stato= new Array(2); 
    // Loop through the Geocoder result set. Note that the results 
    // array will change as this loop can self iterate. 
    for (i = 0; i < results.length; i++) { 

        result = results[i]; 

        types = result.types; 

        for (j = 0; j < types.length; j++) { 

            if (types[j] === 'country') { 

                // If we haven't found the "long_name" property, 
                // then we need to take this object and iterate through 
                // it again by setting it to our master loops array and  
                // setting the index to -1 
                if (result.long_name === undefined) { 
                    results = result.address_components; 
                    i = -1; 
                } 
                // We've found it! 
                else { 
                    stato[0] = result.long_name; 
                    stato[1] = result.short_name; 
                } 

                break; 

            } 

        } 
         
    }                 
    return stato;     
} 
function cerca_locality(results){ 
    city=''; 
    var i, j, 
    result, types; 

    // Loop through the Geocoder result set. Note that the results 
    // array will change as this loop can self iterate. 
    for (i = 0; i < results.length; i++) { 

        result = results[i]; 

        types = result.types; 

        for (j = 0; j < types.length; j++) { 

            if (types[j] === 'locality') { 

                // If we haven't found the "long_name" property, 
                // then we need to take this object and iterate through 
                // it again by setting it to our master loops array and  
                // setting the index to -1 
                if (result.long_name === undefined) { 
                    results = result.address_components; 
                    i = -1; 
                } 
                // We've found it! 
                else { 
                    city = result.long_name; 
                } 

                break; 

            } 

        } 
         
    }     
    return city;         
      }     
      function searchAddress() { 
    var address=document.getElementById("addressInput").value; 
    var mylat=document.getElementById("jform_metadata_lat"); 
    var mylon=document.getElementById("jform_metadata_lng"); 
    var desc=document.getElementById("desc"); 
    var geocoder = new google.maps.Geocoder(); 
    geocoder.geocode({address: address}, function(results, status) { 
        if (status == google.maps.GeocoderStatus.OK) { 
            if (results[0]) { 
                console.log(results[0].geometry.location.lng()); 
                 
                infoWindow.setContent(results[0].formatted_address); 
                mylat.innerHTML=results[0].geometry.location.lat(); 
                mylon.innerHTML=results[0].geometry.location.lng(); 
                // desc.innerHTML=results[0].formatted_address+ '<br>'+results[1].formatted_address+'<br> '+results[2].formatted_address+'<br> '+results[3].formatted_address+'<br> '+results[4].formatted_address+'<br> '+results[5].formatted_address+'<br> '+results[6].formatted_address; 
                infoWindow.setPosition(results[0].geometry.location); 
                infoWindow.open(map); 
                codeLatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng()); 
            } 
        } 
    }); 
     
      } 
      function saveLocationsajax(){ 
    var address = document.getElementById('formattedaddress').innerHTML; 
    var box= document.id('consolebox'); 
    var lat = document.getElementById('jform_metadata_lat').innerHTML; 
    var lon = document.getElementById('jform_metadata_lng').innerHTML; 
    var citta = document.getElementById('jform_metadata_citta').innerHTML; 
        var stato = document.getElementById('jform_metadata_stato').innerHTML;     
        var siglastato = document.getElementById('jform_metadata_siglastato').innerHTML; 
    box.style.display="block"; 
    box.set('html', 'Save in progress...'); 
    var url="{$juri_root}?option=com_aa4j&amp;format=raw&amp;task=refreshGeo&amp;lat="+lat+"&amp;lon="+lon+"&amp;address="+address+"&amp;stato="+stato+"&amp;siglastato="+siglastato+"&amp;citta="+citta; 
    var a=new Request.JSON({ 
        url:url, 
        onFailure: function(){   
            alert('oh nooo!');   
        },   
        onComplete: function(ret){ 
            if (ret){ 
                box.set('html', 'saved'); 
            } else { 
                box.set('html', 'not saved'); 
            } 
            var el = $(box); 
            (function(){ 
                el.set('html', ''); 
            }).delay(1500); 
        } 
    }); 
    a.get(); 
     } 

 

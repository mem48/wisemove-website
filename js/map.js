var map = L.map('map').setView([51.505, -0.09], 13);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
		maxZoom: 18,
		attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
			'<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
			'Imagery ? <a href="https://www.mapbox.com/">Mapbox</a>',
		id: 'mapbox.streets'
}).addTo(map);

//function getColor(d) {
//	return d > 1000 ? '#800026' :
//		   d > 500  ? '#BD0026' :
//		   d > 200  ? '#E31A1C' :
//		   d > 100  ? '#FC4E2A' :
//		   d > 50   ? '#FD8D3C' :
//		   d > 20   ? '#FEB24C' :
//		   d > 10   ? '#FED976' :
//					  '#FFEDA0';
//}
		
function style(feature) {
	return {
		//fillColor: getColor(feature.properties.total),
		fillColor: '#808080',
		weight: 0,
		opacity: 0,
		fillOpacity: 0.7
	};
}


var zones = L.geoJson(null);
//var pricevalues = $('form').serialize();
//var pricevalues = $('priceform');
//var crimevalues = $('crimeform').serialize();
//var pricevalues = document.getElementById("maxprice").value;
//console.log(pricevalues);

map.on('dragend', function onDragEnd(){
zones.clearLayers();
var pricevalues = $('form').serialize();
console.log(pricevalues);

$.ajax({
    		type: "GET",
     		url: "https://www.wisemover.co.uk/api/free.php?bbox=" + map.getBounds().toBBoxString() + '&' + pricevalues,
    		dataType: 'json',
    		success: function (response) {
        		zones = L.geoJson(response, {style: style}, {
            			onEachFeature: function (feature, layer) {
            			layer.bindPopup(feature.properties.total);
            			}
        	});
     	zones.addTo(map);
	 	}});
});
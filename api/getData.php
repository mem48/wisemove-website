<?php
ini_set('display_errors', 1); 

include_once ('connect.php');

# Helper function to get the BBOX
function getBbox ()
{
	# Get the data from the query string
	$bboxString = (isSet ($_GET['bbox']) ? $_GET['bbox'] : NULL);
	
	# Check BBOX is Provided
	if (!$bboxString) {
		echo 'No bbox was supplied.';
	}
	
	# Ensure four values
	if (substr_count ($bboxString, ',') != 3) {
		echo 'An invalid bbox was supplied.';
	}
	
	# Assemble the parameters
	$bbox = array ();
	list ($bbox['w'], $bbox['s'], $bbox['e'], $bbox['n']) = explode (',', $bboxString);
	
	# Ensure valid values
	foreach ($bbox as $key => $value) {
		if (!is_numeric ($value)) {
			echo 'An invalid bbox was supplied.';
		}
	}
	
	# Return the collection
	return $bbox;
}



//get the table and fields data
//$table = $_GET['table'];
//$fields = $_GET['fields'];

//turn fields array into formatted string
//$fieldstr = "";
//foreach ($fields as $i => $field){
//	$fieldstr = $fieldstr . "l.$field, ";
//}

//get the geometry as geojson in WGS84
//$fieldstr = $fieldstr . "ST_AsGeoJSON(ST_Transform(l.geom,4326))";

//Get the BBOX
$bbox = getBbox ();

$parameters = "geometry && ST_MakeEnvelope(" . $bbox['w'] . "," . $bbox['s'] ."," . $bbox['e'] . "," . $bbox['n'] . ", 4326)";
$limit = '100';

// Build SQL SELECT statement and return the geometry as a GeoJSON element in EPSG: 4326
$sql = "SELECT objectid, total, st_asgeojson(geometry) AS geojson FROM zones ";
if (strlen(trim($parameters)) > 0) {
    $sql .= " WHERE " . pg_escape_string($parameters);
}
if (strlen(trim($limit)) > 0) {
    $sql .= " LIMIT " . pg_escape_string($limit);
}


//create basic sql statement
//$sql = "SELECT $fieldstr FROM $table l";

//if a query, add those to the sql statement
//if (isset($_GET['featname'])){
//	$featname = $_GET['featname'];
//	$distance = $_GET['distance'] * 1000; //change km to meters
//
//	//join for spatial query - table geom is in EPSG:26916
//	$sql = $sql . " LEFT JOIN $table r ON ST_DWithin(l.geom, r.geom, $distance) WHERE r.featname = '$featname';";
//}

// echo $sql;

//send the query
if (!$response = pg_query($databaseConnection, $sql)) {
	echo "A query error occured.\n";
	exit;
}

//echo the data back to the DOM
while ($row = pg_fetch_row($response)) {
	foreach ($row as $i => $attr){
		echo $attr.", ";
	}
	echo ";";
}

?>
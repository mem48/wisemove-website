<?php
# In Testing Mode
ini_set ("display_errors", "1");

# Connect to DB
include_once ('connect.php');

###############
## Functions
###############

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

function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
  $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
  $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
  $result = str_replace($escapers, $replacements, $value);
  return $result;
}
	
##############
## Code
##############

#Get the BBOX
$bbox = getBbox ();
#Query Database
# $query = 'SELECT * FROM zones WHERE geometry && ST_MakeEnvelope(:w, :s, :e, :n, 4326) LIMIT 5';
#$query = "SELECT * FROM zones WHERE geometry && ST_MakeEnvelope(" . $bbox['w'] . "," . $bbox['s'] ."," . $bbox['e'] . "," . $bbox['n'] . ", 4326) LIMIT 50";
#echo $query;


$srid = '4326';
$parameters = "geometry && ST_MakeEnvelope(" . $bbox['w'] . "," . $bbox['s'] ."," . $bbox['e'] . "," . $bbox['n'] . ", 4326)";
$fields = '*';
$limit = '1000';
$geomfield = 'geometry';
$geotable = 'zones';

# Build SQL SELECT statement and return the geometry as a GeoJSON element in EPSG: 4326
$sql = "SELECT objectid, total, st_asgeojson(geometry) AS geojson FROM zones ";
if (strlen(trim($parameters)) > 0) {
    $sql .= " WHERE " . pg_escape_string($parameters);
}
if (strlen(trim($limit)) > 0) {
    $sql .= " LIMIT " . pg_escape_string($limit);
}
#echo $sql;

$rs = pg_query($databaseConnection, $sql) or die('Query failed: ' . pg_last_error());

# Build GeoJSON
$output    = '';
$rowOutput = '';
while ($row = pg_fetch_assoc($rs)) {
    $rowOutput = (strlen($rowOutput) > 0 ? ',' : '') . '{"type": "Feature", "geometry": ' . $row['geojson'] . ', "properties": {';
    $props = '';
    $id    = '';
    foreach ($row as $key => $val) {
        if ($key != "geojson") {
            $props .= (strlen($props) > 0 ? ',' : '') . '"' . $key . '":"' . escapeJsonString($val) . '"';
        }
        if ($key == "id") {
            $id .= ',"id":"' . escapeJsonString($val) . '"';
        }
    }
    
    $rowOutput .= $props . '}';
    $rowOutput .= $id;
    $rowOutput .= '}';
    $output .= $rowOutput;
}
$output = '{ "type": "FeatureCollection", "features": [ ' . $output . ' ]}';
echo $output;

# Free resultset
pg_free_result($rs);

# Closing connection
pg_close($databaseConnection);
	
?>




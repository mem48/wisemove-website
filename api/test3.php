<html>
 <head>
  <title>PHP Test</title>
 </head>
 <body>
	<?php
	# In Testing Mode
	ini_set ("display_errors", "1");
	echo "<p>Running Tests v4.2</p>";
	
	# Connect to DB
	include_once ('connect.php');
	
	###############
	## Functions
	###############
	
	# Helper function to get the BBOX
	private function getBbox (&$error = false)
	{
		# Get the data from the query string
		$bboxString = (isSet ($_GET['bbox']) ? $_GET['bbox'] : NULL);
		
		# Check BBOX is Provided
		if (!$bboxString) {
			$error = 'No bbox was supplied.';
			return false;
		}
		
		# Ensure four values
		if (substr_count ($bboxString, ',') != 3) {
			$error = 'An invalid bbox was supplied.';
			return false;
		}
		
		# Assemble the parameters
		$bbox = array ();
		list ($bbox['w'], $bbox['s'], $bbox['e'], $bbox['n']) = explode (',', $bboxString);
		
		# Ensure valid values
		foreach ($bbox as $key => $value) {
			if (!is_numeric ($value)) {
				$error = 'An invalid bbox was supplied.';
				return false;
			}
		}
		
		# Return the collection
		return $bbox;
	}
	
	# Function to convert to GeoJSON
	private function asGeojson ($data)
	{
		# Format the output as GeoJSON
		$geojson = array ();
		$geojson['type'] = 'FeatureCollection';
		$geojson['features'] = array ();
		foreach ($data as $row) {
			$properties = $row;
			unset ($properties['geometry']);
			$geojson['features'][] = array (
				'type' => 'Feature',
				'geometry' => json_decode ($row['geometry'], true),
				'properties' => $properties,
			);
		}
		
		# Return the GeoJSON array structure
		return $geojson;
	}
	
	# Error response
	private function error ($string)
	{
		# Assemble and return the error
		$data = array ('error' => $string);
		return $this->responseJson ($data);
	}
	
	
	# Function to transmit data as JSON
	private function responseJson ($jsonArray, $downloadFile = false /* or filename */)
	{
		# Allow any client to connect, and permit on localhost
		header ('Access-Control-Allow-Origin: *');
		
		# If forcing as a download, send the header
		if ($downloadFile) {
			header ('Content-disposition: attachment; filename=' . $downloadFile);
		}
		
		# Send the response, encoded as JSON
		header ('Content-Type: application/json');
		echo json_encode ($jsonArray, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
	}
	
	##############
	## Code
	##############
	
	#Get the BBOX
	$bbox = getBbox();
	
	#Query Database
	# $query = 'SELECT * FROM zones WHERE geometry && ST_MakeEnvelope(:w, :s, :e, :n, 4326) LIMIT 50';
	$query = 'SELECT * FROM zones WHERE geometry && ST_MakeEnvelope($bbox['w'], $bbox['s'], $bbox['e'], $bbox['n'], 4326) LIMIT 50';
	echo $query;
	$result = pg_query($databaseConnection, $query) or die('Query failed: ' . pg_last_error());
	
	
	
	# Free resultset
	pg_free_result($result);

	# Closing connection
	pg_close($databaseConnection);
		
	?>
 </body>
</html>



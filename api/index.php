 <?php
 
 #Api Call to get PCT data
 # Define the settings, using the credentials above
  $connection_string = "host = wisemovedb.cf6eckl151qq.us-east-2.rds.amazonaws.com port = 5432 dbname = wisemovedb user = wisemoveadmin password = 85D$N^$lnQUoNGO3q3f4";
 
 # Connect to the database
 # We use the PDO database abstraction library, and provide a DSN connection string in this format: 'pgsql:host=localhost;dbname=example'
 try {
 	# $databaseConnection = new PDO ("pgsql:host={$settings['hostname']};dbname={$settings['database']}", $settings['username'], $settings['password']);
 	# $databaseConnection->setAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION, PDO::FETCH_ASSOC);
 	
 	$databaseConnection =  pg_connect( $connection_string );
 	
 } catch (PDOException $e) {
 	print "Error!: " . $e->getMessage ();
 	die;
 }
 
 #Get Variables
 $bbox = (isSet ($_GET['bbox']) ? $_GET['bbox'] : '');

 
 #Check BBOX is Provided
 if (!$bbox) {
 	$response = array ('error' => "No bbox was supplied.");
 	echo json_encode ($response);
 	die;
 }
 
 #Check BBOX is valid
 list ($w, $s, $e, $n) = explode (',', $bbox);
 
 if(!(is_numeric($w) && is_numeric($s) && is_numeric($e) && is_numeric($n) )){
   $response = array ('error' => "BBox was invalid");
 	echo json_encode ($response);
 	die;
 }
 
 
 # Construct the query
 $query = "
 	SELECT
 		objectid, total, geometry,
 		ST_AsGeoJSON(geometry) AS geometry
 	FROM oa
 	WHERE geometry && ST_MakeEnvelope(:w, :s, :e, :n, 4326)
 	AND :layer > 0
 	LIMIT 500
 ;";
 

 # Select the data
 $preparedStatement = $databaseConnection->prepare ($query);
 #$preparedStatement->bindParam (':layer', $layer);
 $preparedStatement->bindParam (':w', $w);
 $preparedStatement->bindParam (':s', $s);
 $preparedStatement->bindParam (':e', $e);
 $preparedStatement->bindParam (':n', $n);
 $data = array ();
 if ($preparedStatement->execute ()) {
 	while ($row = $preparedStatement->fetch ()) {
 		$data[] = $row;
 	}
 }
 
 
 #Format the output as GeoJSON
 foreach ($data as $index => $row) {
 	$data[$index]['geotext'] = json_decode ($row['geotext'], true);
 }
 
 $geojson = array ();
 $geojson['type'] = 'FeatureCollection';
 $geojson['features'] = array ();
 foreach ($data as $row) {
 	$properties = $row;
 	unset ($properties['geotext']);
 	$geojson['features'][] = array (
 		'type' => 'Feature',
 		'geometry' => $row['geotext'],
 		'properties' => $properties,
 	);
 }
 
 header ('Content-Type: application/json');
 echo json_encode ($geojson, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
 
 
 ?>
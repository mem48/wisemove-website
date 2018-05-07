<html>
 <head>
  <title>PHP Test</title>
 </head>
 <body>
	<?php
	# Load and run the API class
	require_once ('./defaults.php');

	echo "Running Tests";

	# Class properties
	private $databaseConnection;

	# Supported formats
	private $formats = array ('json', 'geojson', 'csv');

		

	# Load settings
	$this->settings = $this->defaults ();

	# Load subclass
	require_once ('./wisemoveModel.php');

	echo $this;
		
	# Connect to the database, providing a DSN connection string in this format: 'pgsql:host=localhost;dbname=example'
	try {
		$this->databaseConnection = new PDO ("pgsql:host={$this->settings['hostname']};dbname={$this->settings['database']}", $this->settings['username'], $this->settings['password']);
		$this->databaseConnection->setAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->databaseConnection->setAttribute (PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		// var_dump ($e->getMessage (), $query);
		return $this->error ('Unable to connect to the database.');
	}

	# Ensure a valid format has been supplied
	$format = $this->getFormat ($error);
	if ($error) {
		return $this->error ($error);
	}

		
	# Load the model, passing in API parameters
	$this->wisemoveModel = new wisemoveModel ($bbox, $zoom, $_GET);

	# Ensure a valid action has been supplied
	$method = $this->getMethod ($error);
	if ($error) {
		return $this->error ($error);
	}
	?>
 </body>
</html>



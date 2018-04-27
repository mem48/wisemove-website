<?php

# wisemove model
class wisemoveModel
{
	# Class properties
	private $tablePrefix = false;


	# Constructor
	public function __construct ($bbox, $zoom, $get)
	{
		# Set values provided by the API
		$this->bbox = $bbox;	// Validated
		$this->zoom = $zoom;	// Validated
		$this->get = $get;		// Unvalidated contents of $_GET, i.e. query string values

	}


	# Beta mode
	public function enableBetaMode ()
	{
		$this->tablePrefix = 'alt_';
	}


	/*
	# Example model
	public function exampleModel (&$error = false)
	{
		// Logic assembles the values returned below
		// ...

		# Return the model
		return array (
			'table' => 'table',
			'fields' => $fields,			// Fields to retrieve
			'constraints' => $constraints,	// Database constraints
			'parameters' => $parameters,	// Parameters, e.g. :w for bbox west
			'limit' => $limit,				// Limit of data returned
		);
	}
	*/


	# Recommended infrastructure
	public function basicModel (&$error = false)
	{
		# Base values
		$fields = array (
			// 'id',
			'basic',
		);
		$constraints = array (
			'geometry && ST_MakeEnvelope(:w, :s, :e, :n, 4326)'
		);
		$parameters = $this->bbox;
		$limit = false;

		# Set filters based on zoom
		switch (true) {

			# Max Zoomed Out
			case ($this->zoom == 11):
				$fields[] = 'ST_AsGeoJSON(ST_Simplify(geometry, 0.00412 )) AS geometry';
				$limit = 5000;
				break;

			case ($this->zoom == 12):
				$fields[] = 'ST_AsGeoJSON(ST_Simplify(geometry, 0.00206 )) AS geometry';
				$limit = 5000;
				break;

			case ($this->zoom == 13):
				$fields[] = 'ST_AsGeoJSON(ST_Simplify(geometry, 0.00103 )) AS geometry';
				$limit = 5000;
				break;

			case ($this->zoom == 14):
				$fields[] = 'ST_AsGeoJSON(ST_Simplify(geometry, 0.00052 )) AS geometry';
				$limit = 5000;
				break;

			case ($this->zoom >= 15):
				$fields[] = 'ST_AsGeoJSON(geometry) AS geometry';
				$limit = 5000;
				break;

		  #Max Zoomed In

			# Show nothing if too zoomed out
			default:
				$error = 'Please zoom in.';
				return false;
		}

		# Return the model
		return array (
			'table' => $this->tablePrefix . 'zones',
			'fields' => $fields,
			'constraints' => $constraints,
			'parameters' => $parameters,
			'limit' => $limit,
		);
	}


	# Documentation
	public static function basicDocumentation ()
	{
		return array (
			'name' => 'basic',
			'example' => '/api/v1/basic.json?bbox=-2.6404,51.4698,-2.5417,51.4926&zoom=15',
			'fields' => array (
				'bbox' => '%bbox',
				'zoom' => '%zoom',
			),
		);
	}

}

?>

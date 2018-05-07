<html>
 <head>
  <title>PHP Test</title>
 </head>
 <body>
 <?php
	ini_set ("display_errors", "1");
	echo "<p>Running Tests v4.3</p>";
	include_once ('connect.php');
	// Check Connection
	
	// Performing SQL query
	$query = 'SELECT * FROM zones LIMIT 5';
	$result = pg_query($databaseConnection, $query) or die('Query failed: ' . pg_last_error());

	// Printing results in HTML
	echo "<table>\n";
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
		echo "\t<tr>\n";
		foreach ($line as $col_value) {
			echo "\t\t<td>$col_value</td>\n";
		}
		echo "\t</tr>\n";
	}
	echo "</table>\n";

	// Free resultset
	pg_free_result($result);

	// Closing connection
	pg_close($databaseConnection);
 ?>
 </body>
</html>
<html>
 <head>
  <title>PHP Test</title>
 </head>
 <body>
 <?php
	echo "Running Tests v3";
	include_once ('./connect.php');
	// Performing SQL query
	$query = 'SELECT * FROM zones LIMIT 5';
	$result = pg_query($query) or die('Query failed: ' . pg_last_error());

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
	pg_close($dbconn);
 ?>
 </body>
</html>
<html>
 <head>
  <title>PHP Test</title>
 </head>
 <body>
 <?php
	echo "Running Tests";
	require_once ('./defaults.php');
	$defaults = defaults ();
	echo $defaults;
	echo "Getting Settings";
	$this->settings = $this->defaults ();
	echo $this;
 ?>
 </body>
</html>
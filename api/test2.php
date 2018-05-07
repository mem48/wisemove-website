<html>
 <head>
  <title>PHP Test</title>
 </head>
 <body>
 <?php
	echo "Running Tests";
	require_once ('./defaults.php');
	$this->settings = $this->defaults ();
	echo $this;
 ?>
 </body>
</html>
<?PHP
// TODO: Completely redo this command
	require_once('cti.php');
	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}

	$_SESSION['Board'] = -3;
	require_once('post.php');
	
	require_once('end.php');
?>
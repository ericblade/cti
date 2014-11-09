<?PHP
require_once('cti.php');
	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}

echo "Mail system is currently down, sorry.<BR>";
$_SESSION['command'] = "";
require_once('end.php');
?>
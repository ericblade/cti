<?PHP
	require_once('cti.php');
	
	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}
	
	$sql = sprintf("UPDATE Users SET HomeX=%d,HomeY=%d WHERE userid=%d", $user['LocX'], $user['LocY'], $user['userid']);
	mysql_query($sql);
	
	echo "Home Node is now ($user[LocX],$user[LocY]).<BR>";
	
	require_once('end.php');
?>
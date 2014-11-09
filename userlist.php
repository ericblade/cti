<?PHP
	require_once('cti.php');

	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}
	
	$sql = "SELECT * FROM Users";
	$u = get_query_array($sql);
	echo '<table>';
	echo '<tr>';
	for($x = 0; $x < sizeof($u); $x++) {
		if(!($x % 5) && $x != 0) 
			echo '</tr><tr>';
		echo '<td>'.$u[$x]['Name'].'</td>';
	}
	echo '</tr></table>';
	$_SESSION['command'] = "";
	
	require_once('end.php');
?>
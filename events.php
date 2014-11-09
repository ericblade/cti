<?PHP
	require_once('cti.php');

	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}
	
	if(!loggedin()) {
		echo "I'm sorry, I can not allow that.";
		require_once('end.php');
	}
	$start = $_GET['start'];
	switch($version) {
		case "3.0":
			$sql = sprintf("SELECT * FROM EventLog ORDER BY Timesent DESC LIMIT %d, 30", $start);
			break;
		case "2.0":
			$sql = sprintf("SELECT * FROM EventLog WHERE (Level <= ".$user['Level']." AND EVENT NOT LIKE '%%logged%%') ORDER BY Timesent DESC LIMIT %d, 30", $start);
			break;
		default:
			$sql = sprintf("SELECT * FROM EventLog WHERE (Level <= ".$user['Level']." AND EVENT LIKE '%%logged%%') ORDER BY Timesent DESC LIMIT %d, 30", $start);
			break;
	}
	
	$events = get_query_array($sql);
	
	echo '<table>';
	echo '<tr><th>Time</th><th>Event Logged</th></tr>';
	foreach($events as $event) {
		echo '<tr><td>'.mydate($event['Timesent']).'</td><td>'.stripslashes($event['Event']).'</td></tr>';
	}
	echo '</table>';
	printf('<a href="%s?start=%d">Older</a><br>', $_SERVER['PHP_SELF'], $start+30);

	require_once('end.php');
?>
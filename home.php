<?PHP
	$warezname="Home";
	$version="1.0";
	require_once('cti.php');
	
	if(!loggedin() || $version < 1) {
		echo "You do not have access to that.<BR>";
		require_once('end.php');
	}
	$oldlevel = get_level_number(get_distance_from_base($user['LocX'], $user['LocY']));
	if($version == "2.0") {
		printf("Returning to Home Node (%d,%d)<BR>", $user[HomeX], $user[HomeY]);
		$user['LocX'] = $user['HomeX'];
		$user['LocY'] = $user['HomeY'];
	} else {
		// TODO: Have this return to user base location if one is set
		$user['LocX'] = 0;
		$user['LocY'] = 0;
		echo "Returning to Loc 0,0";
	}
	$_SESSION['command'] = "";
	$newlevel = get_level_number(get_distance_from_base($user['LocX'], $user['LocY']));
	if($oldlevel != $newlevel) {
		if($newlevel == 0)
			echo "<P>You are at the Primary Access Point.<BR>";
		else
			echo "<P>You have entered the ".get_level_title(get_distance_from_base($user['LocX'], $user['LocY']))." Zone.<BR>";
	}

	display_location_info();
	$_SESSION['Board'] = get_level_number(get_distance_from_base($user['LocX'], $user['LocY']));
	require_once('end.php');
?>
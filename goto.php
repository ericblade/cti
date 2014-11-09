<?PHP
	require_once('cti.php');

	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}
	
	$oldlevel = get_level_number(get_distance_from_base($user['LocX'], $user['LocY']));
	if($_GET['name']) {
		echo "Locating $_GET[name], for 5 credits...<BR>";
		$user['Stamina'] -= 5;
		$u = get_user($_GET['name']);
		if(!$u) {
			echo "Unable to locate.<BR>";
		} else {
			$_POST['x'] = $u['LocX'];
			$_POST['y'] = $u['LocY'];
		}
	}
	if($_POST['x'] == "" || $_POST['y'] == "") {
		echo '<form name="goto" method="post" action="'.$_SERVER['PHP_SELF'].'"><p>
		Goto LocX:<input type="text" size="4" name="x"><br>
		Goto LocY:<input type="text" size="4" name="y"><br>
		<input class="submit" type="submit" value="Go"></form>';
	} else {
		$_SESSION['command'] = "";
		//$temp = get_distance_from_point($_POST['x'], $_POST['y'], $user['LocX'], $user['LocY']);
		//echo "Range=$temp<BR>";
		if(get_distance_from_base($_POST['x'], $_POST['y']) > $user['Level']) {
			echo "A valiant effort, but you don't have the proper clearance to reach that location.<BR>";
			require_once('end.php');
		} else if($_POST['x'] != 0 && $_POST['y'] != 0 && $user['LocX'] != 0 && $user['LocY'] != 0 && !same_sector($user['LocX'], $user['LocY'], $_POST['x'], $_POST['y'])) { //else if(get_distance_from_point($_POST['x'], $_POST['y'], $user['LocX'], $user['LocY']) > $user['Level']) {
			echo "No route available to that location.<BR>";
		} else {
			echo "Moved.";
			$user['LocX'] = (int)$_POST['x'];
			$user['LocY'] = (int)$_POST['y'];
		}
	}
	$newlevel = get_level_number(get_distance_from_base($user['LocX'], $user['LocY']));
	if($oldlevel != $newlevel) {
		echo "<P>You have entered the ".get_level_title(get_distance_from_base($user['LocX'], $user['LocY']))." Zone.<BR>";
	}
	display_location_info();
	$_SESSION['Board'] = get_level_number(get_distance_from_base($user['LocX'], $user['LocY']));
	
	require_once('end.php');
?>
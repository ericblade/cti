<?PHP
	require_once('cti.php');

	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}
	
	if(!$_POST['name']) {
		user_select_form(0, 1);
		require_once('end.php');
	} else {
		$u = get_user($_POST['name']);
		if(!$u) {
			echo "User '$_POST[name]' not found.<BR>";
			require_once('end.php');
		}
		$res = valid_target($user, $u);
		if($res != 1) {
			echo "$res<BR>";
			require_once('end.php');
		}

		$u['Level'] = round($u['Level'] * 0.2);
		$u['Stamina'] = round($u['Stamina'] * 0.2);
		$u['Fame'] = round($u['Fame'] * 0.8);
		if($u['Name'] == $user['Name']) {
			$user['Level'] = $u['Level'];
			$user['Stamina'] = round($u['Stamina'] * 0.1);
			$user['Fame'] = $u['Fame'];
		}
		save_user($u);
		log_event($user['Name'] . " reset " . $u['Name']." ($_POST[comment])", 0);
		send_tell($user['Name'], $u['userid'], "$user[Name] reset you ($_POST[comment]");
		$user['Fame'] += $u['Level'];
		echo "Done.<BR>";
		$_SESSION['command'] = "";
	}
	require_once('end.php');
?>
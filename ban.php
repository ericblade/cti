<?PHP
	$warezname="Ban";
	$version="1.0";
	
	require_once('cti.php');

	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}
	
	//print_r($_GET);
	//echo "<BR>";
	//print_r($_POST);
	//echo "<BR>";
	
	// TODO: have banned user's credits returned to a pool
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

		if(!ban_user($u['Name'])) {
			echo "Error banning user.  Sorry!<BR>";
		} else {
			$_SESSION['command'] = "";
			echo "User banned.<BR>";
			log_event(sprintf("%s banned %s (%s)", $user['Name'], $u['Name'], $_POST['comment']), 0);
			$user['Fame'] += $u['Level'];
	
			if($u['Name'] == $user['Name']) {
				echo "Hey, you do realise that you just banned yourself, right?  Hope so, as this cant be undone.<BR>";
				echo "Logged out.<BR>";
				unset($_SESSION['username']);
				session_destroy();
				unset($user);
				require_once('end.php');
			}
		}
	}
	require_once('end.php');
?>
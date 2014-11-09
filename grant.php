<?PHP
	require_once('cti.php');
	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}

	if(!$_POST['name'] || !$_POST['amount']) {
		user_select_form(1,1);
		require_once('end.php');
	} else {
		$u = get_user($_POST['name']);
		if(!$u) {
			echo "User '$_POST[name]' not found.<BR>";
			require_once('end.php');
		}
		$cost = ($_POST['amount'] - 1) * $command['Cost'];
		if($cost > $user['Stamina'] + $command['Cost']) {
			echo "You can't afford that.<BR>";
			require_once('end.php');
		}
		$res = valid_target($user, $u);
		if($res != 1) {
			echo "$res<BR>";
			require_once('end.php');
		}
		
		if( ($u['level'] + $_POST['amount']) > ($user['Level'] - 1) ) {
			echo "Error: Can not grant beyond your own level.<BR>";
			require_once('end.php');
		}
		
		if($_POST['amount'] < 0) {
			echo "Wow, why didn't I think of that?<BR>";
			require_once('end.php');
		}

		$u['Level'] += $_POST['amount'];
		$user['Stamina'] -= $cost;
		save_user($u);
		log_event(sprintf("%s granted %s %d level%s (%s)", 
			$user['Name'], $u['Name'], $_POST['amount'], ($_POST['amount'] > 1 ? "s":""), $_POST['comment']), 0);
		send_tell($user['Name'], $u['userid'], sprintf("%s granted you %d level%s (%s)", 
												$user['Name'], $_POST['amount'], ($_POST['amount'] > 1 ? "s" : ""), $_POST['comment']));
		echo "Done.<BR>";
		$_SESSION['command'] = "";
	}
	require_once('end.php');

?>
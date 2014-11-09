<?PHP
	
	require_once('cti.php');

	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}

	if(!$_POST['name'] || !$_POST['amount']) {
		user_select_form(1, 1);
		require_once('end.php');
	} else {
		if($_POST['amount'] < 0) {
			echo "Ha. ha. ha.  Funny.<BR>";
			require_once('end.php');
		}
	
		$u = get_user($_POST['name']);
		if(!$u) {
			echo "User '$_POST[name]' does not exist.<BR>";
			require_once('end.php');
		}
		$res = valid_target($user, $u);
		if($res != 1 && $u['userid'] != $user['userid']) {
			echo "$res<BR>";
			require_once('end.php');
		}
		
		$cost = ($_POST['amount'] - 1) * $command['Cost'];
		if($cost > $user['Stamina'] + $command['Cost']) {
			echo "You can't afford that.<BR>";
			require_once('end.php');
		}
		if($_POST['amount'] < 0) {
			echo "Wow, why didn't I think of that?<BR>";
			require_once('end.php');
		}
		//if($user['Stamina'] - ( ($_POST['amount'] - 1) * $command['Cost']) 
		$u['Level'] -= $_POST['amount'];
		if($u['Name'] == $user['Name']) $user['Level'] -= $_POST['amount'];
		$user['Stamina'] -= ($_POST['amount']-1) * ($command['Cost']);
		save_user($u);
		log_event(sprintf("%s revoked %d level%s from %s (%s)",
				$user['Name'], $_POST['amount'], ($_POST['amount']>1?"s":""), $u['Name'], $_POST['comment']), 0);
		send_tell($user['Name'], $u['userid'], sprintf("%s revoked %d level%s (%s)", 
				$user['Name'], $_POST['amount'], ($_POST['amount']>1?"s":""), $_POST['comment']));
		$user['Fame'] += (0.5 * $_POST['amount']);
		echo "Done.<BR>";
		$_SESSION['command'] = "";
	}
	require_once('end.php');
?>
<?php
// TODO: build %USERMENU% and %MESSAGEMENU% into templates
	require_once('cti.php');

	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}
	
	if(!$_POST['name'] || !$_POST['amount']) {
		user_select_form(1,1);
		require_once('end.php');
	} else {
		if($_POST['amount'] < 0) {
			echo "Ha. ha. ha.  Funny.<BR>";
			require_once('end.php');
		}

		$u = get_user($_POST['name']);
		$cost = ($_POST['amount'] - 1) * $command['Cost'];
		if($cost > $user['Stamina'] + $command['Cost']) {
			echo "You can't afford that.<BR>";
			require_once('end.php');
		}

		if(!$u) {
			echo "User '$_POST[name]' not found.<BR>";
			require_once('end.php');
		}
		$res = valid_target($user, $u);
		if($res != 1) {
			echo "$res<BR>";
			require_once('end.php');
		}

		$u['Stamina'] -= 10 * $_POST['amount'];
		$user['Stamina'] -= $command['Cost'] * ($_POST['amount'] - 1);
		if($user['Name'] == $u['Name'])
			$user['Stamina'] -= 10 * $_POST['amount'];
		save_user($u);
		log_event(sprintf("%s hacked %s for %d (%s)", $user['Name'], $u['Name'], $_POST['amount']*10, $_POST['comment']), 0);
		send_tell($user['Name'], $u['userid'], sprintf("%s hacked you for %d (%s)",
			$user['Name'], $_POST['amount']*10, $_POST['comment']));
		$user['Fame'] += (0.25 * $_POST['amount']);
		echo "Done.<BR>";
		$_SESSION['command'] = "";
	}
	require_once('end.php');

?>
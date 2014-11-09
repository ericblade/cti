<?php
// TODO: build %USERMENU% and %MESSAGEMENU% into templates
	require_once('cti.php');
	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}

	if(!$_POST['name'] || !$_POST['amount']) {
		user_select_form(1);
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
		if($res != 1) {
			echo "$res<BR>";
			require_once('end.php');
		}
		if($_POST['amount'] < 0) {
			echo "Wow, why didn't I think of that?<BR>";
			require_once('end.php');
		}
		$u['Stamina'] -= 10 * $_POST['amount'];
		$user['Stamina'] -= $command['Cost'] * ($_POST['amount'] - 1);
		if($u['Name'] == $user['Name']) $user['Stamina'] -= 10 * $_POST['amount'];
		save_user($u);
		log_event($user['Name'] . " stole " . ($_POST['amount'] * 10) . " from " . $u['Name'] , 0);
		send_tell($user['Name'], $u['userid'], "$user[Name] stole " . ($_POST['amount'] * 10) . " from you.");
		$user['Fame'] += (0.25 * $_POST['amount']);
		echo "Done.";
		$_SESSION['command'] = "";
	}
	require_once('end.php');
?>
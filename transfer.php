<?PHP
	// TODO: just replace this and the other stuff with some banking app? :D
	require_once('cti.php');

	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}
	//if($_GET['user']) $_POST['user'] = $_GET['user'];
	//if($_GET['amount']) $_POST['amount'] = $_GET['amount'];
	if(!$_POST['name'] || !$_POST['amount']) {
		user_select_form(1,1);
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
		if($u['Name'] == $user['Name']) {
			echo "Invalid user.<BR>";
			require_once('end.php');
		}
		if($_POST['amount'] > $user['Stamina']) {
			echo "You don't have that much to give.<BR>";
			require_once('end.php');
		}
		if($_POST['amount'] < 0) {
			echo "Wow, why didn't I think of that?<BR>";
			require_once('end.php');
		}
		$u['Stamina'] += $_POST['amount'];
		$user['Stamina'] -= $_POST['amount'];
		log_event(sprintf("%s transferred %d credit%s to %s (%s)",
				$user['Name'], $_POST['amount'], ($_POST['amount'] > 1 ? "s" : ""), $u['Name'], $_POST['comment']), 0);
		send_tell($user['Name'], $u['userid'], sprintf("%s transferred you %d credit%s (%s)", 
				$user['Name'], $_POST['amount'], ($_POST['amount'] > 1 ? "s" : ""), $_POST['comment']));
		$user['Fame'] += (0.25 * $_POST['amount']);
		save_user($u);
		// and then this player will save automatically down at the end...
		echo "Done.<BR>";
		$_SESSION['command'] = "";
	}
	
	require_once('end.php');
?>
<?PHP
	require_once('cti.php');
	
	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}
	if(!$_POST['message']) {
		echo '<fieldset><legend>Tell User</legend><form name="tell" method="post" action="'.$_SERVER['PHPSELF'].'"><p>';
		echo '<label for="name">User:</label><input class="text" type="text" name="name" value="'.$_GET['name'].'" '.$INPUTJS.'><br>';
		echo '<label for="message">Message:</label><input type="text" maxlength="200" class="text" name="message" '.$INPUTJS.'><br>';
		echo '<input class="submit" type="submit" value="Send" '.$INPUTJS.'>';
		echo '</form></fieldset>';
	} else {
		$u = get_user($_POST['name']);
		if(!$u) {
			echo "Can't find user '$_POST[name]'<BR>";
			require_once('end.php');
		}
		send_tell($user['Name'], $u['userid'], "$user[Name] tells you: ".$_POST['message'], 0);
		$_SESSION['command'] = "";
		echo "Message sent.<BR>";
		$user['Fame'] += 0.05;
	}
	require_once('end.php');
?>
<?PHP
	require_once('cti.php');
	
	if(!loggedin() || $version < 1 && $user['userid'] != 1) {
		echo "Try logging in first, Jack.<BR>Ass.<BR>";
		require_once('end.php');
	}
	
	if(!$_POST['boardname'] || $_POST['confirm'] != "YES")  {
		echo '<fieldset><legend>'.$warezname.' ' . $version.'</legend>';
		echo '<form name="createboard" action="'.$_SERVER['PHPSELF'].'" method="post"><p>';
		echo '*** WARNING: THIS PROGRAM HAS NO CONFIRMATION PROMPTS, AND IS HIGHLY DESTRUCTIVE.  USE AT YOUR OWN RISK.<BR>';
		echo '*** Messages within Destroyed boards may be scattered throughout the network!<BR>';
		echo '<label for="boardname">Board Name:</label><input type="text" name="boardname" '.$INPUTJS.'><br>';
		echo '<label for="confirm">Type YES to Confirm:</label><input type="text" name="confirm" '.$INPUTJS.'<br>';
		echo '<input class="submit" type="submit" value="Destroy" '.$INPUTJS.'>';
		echo '</form>';
		echo '</fieldset>';
	} else {
		$_POST['boardname'] = fix_text($_POST['boardname']);
		$sql = sprintf("SELECT * FROM Boards WHERE boardname='%s'", $_POST['boardname']);
		$res = get_query($sql);
		if(!$res) {
			echo "I can't find a board by that name!<BR>";
			require_once('end.php');
		}
		if($res['boardid'] < 12) {
			echo "No one may remove system boards.<BR>";
			require_once('end.php');
		}
		if($res['Destroyed']) {
			echo "Board is already destroyed!<BR>";
			require_once('end.php');
		}
		$sql = sprintf("UPDATE Boards SET Destroyed=1 WHERE boardname='%s' LIMIT 1", $_POST['boardname']);
		$res = get_query($sql);
		echo "*** $_POST[boardname] HAS BEEN DESTROYED!";
		log_event($user['Name'] . " destroyed the board called '".$_POST['boardname']."'.", 0);
	}
	require_once('end.php');
?>
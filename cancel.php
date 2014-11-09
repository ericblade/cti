<?php
	require_once('cti.php');
		// TODO: make the rename function not ever allow the name renamed FROM to be used again, so you can't rename to someone else's old name and cancel their messages for cheap
		// TODO: we'll need a boardid to board name function
	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}

	if(!$_GET['msgnum']) {
		echo '<form name="cancel" method="get" action="'.$_SERVER['PHP_SELF'].'">
			<p>Enter message number to cancel:<input type="text" name="msgnum" maxlength="6" size="6"><br>
			<input class="submit" type="submit" value="Cancel Message"></form>';
	} else {
		$_SESSION['command'] = "";
			$msg = get_query("SELECT * FROM Messages WHERE ((deleted is null or deleted = 0) and msgid = ".$_GET['msgnum'].")");
			
		$sql = sprintf("SELECT boardname FROM boards WHERE boardid=%d", $msg['boardid']);
		$bn = get_query($sql);
		$bn = $bn['boardname'];

		switch($version) {
			case "1.0":
				if(!$msg)
					echo "I can't find message #".$_GET['msgnum'].".";
				else if($msg['sender'] != $user['Name'])
					echo "Cancelling of messages other than your own is not supported by this version of Cancel.";
				else if(!cancel_message($msg['msgid']))
					echo "Error cancelling message.";
				else {
					log_event($user['Name']." cancelled their own message #".$msg['msgid']." on ".$bn, $user['Level']);
					echo "Message cancelled.";
				}
				break;
			case "2.0":
				if(!$msg)
					echo "I can't find message #".$_GET['msgnum'].".";
				else if(!cancel_message($msg['msgid']))
					echo "Error cancelling message.";
				else {
					log_event($user['Name']." cancelled message #".$msg['msgid']." on ".$bn." by ".$msg['sender'].".", $user['Level']);
					if($user['Name'] != $msg['sender']) {
						$user['Fame'] += 1;
					}
					echo "Message cancelled.";
				}
				break;
		}
	}
	
	require_once('end.php');
?>
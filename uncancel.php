<?php
	require_once('cti.php');
		// TODO: make the rename function not ever allow the name renamed FROM to be used again, so you can't rename to someone else's old name and cancel their messages for cheap
		// TODO: we'll need a boardid to board name function
	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}

	if(!$_GET['msgnum']) {
		echo '<form name="uncancel" method="get" action="'.$_SERVER['PHP_SELF'].'">
			<p>Enter message number to cancel:<input type="text" name="msgnum" maxlength="6" size="6"><br>
			<input class="submit" type="submit" value="UnCancel Message"></form>';
	} else {
		$_SESSION['command'] = "";
			$msg = get_query("SELECT * FROM Messages WHERE ((deleted = 1) and msgid = ".$_GET['msgnum'].")");
		switch($version) {
			case "1.0":
				if(!$msg)
					echo "I can't find message #".$_GET['msgnum'].".";
				else if($msg['sender'] != $user['Name'])
					echo "Cancelling of messages other than your own is not supported by this version of Cancel.";
				else if(!uncancel_message($msg['msgid']))
					echo "Error uncancelling message.";
				else {
					log_event($user['Name']." uncancelled their own message #".$msg['msgid']." on board ".$msg['boardid'], $user['Level']);
					echo "Message uncancelled.";
				}
				break;
			case "2.0":
				if(!$msg)
					echo "I can't find message #".$_GET['msgnum'].".";
				else if(!uncancel_message($msg['msgid']))
					echo "Error uncancelling message.";
				else {
					log_event($user['Name']." uncancelled message #".$msg['msgid']." on board ".$msg['boardid']." by ".$msg['sender'].".", $user['Level']);
					echo "Message uncancelled.";
				}
				break;
		}
	}
	
	require_once('end.php');
?>
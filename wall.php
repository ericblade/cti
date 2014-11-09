<?PHP
	require_once('cti.php');

	if(!$_POST['message']) {
		?>
		<fieldset><legend>Broadcast To All Online</legend>
		<form name="wall" method="post" action="<?PHP echo $_SERVER['PHP_SELF'];?>">
		<p>
		<label for="message">Message:</label><input type="text" class="text" name="message" maxlength="225"><br>
		<input type="submit" value="Send">
		</form>
		</fieldset>
		<?PHP
		require_once('end.php');
	} else {
		$sql = sprintf("SELECT * FROM Users WHERE lastcmdtime > %d", time() - (3600)); // last hour
		$users = get_query_array($sql);
		if($users) {
			foreach($users as $u) {
				if($u['userid'] != $user['userid']) {
					send_tell($user['Name'], $u['userid'], "Broadcast from $user[Name]:".$_POST['message'], 0);
					$count++;
				}
			}
			if($count > 0) {
				echo "Message broadcast to $count users.<BR>";
			} else {
				echo '<img style="float:left; width:30%; margin: 5px" src="images/artemis.jpg" alt="Oops!">';
				echo "How lonely, there's no one here to receive that broadcast. Sorry!<BR>";
			}
		} else {
			echo "No one logged in.<BR>";
		}
	}
	require_once('end.php');
?>
<?php
	$warezname="NewUser";
	$version="1.0";
	
	require_once('cti.php');
	
	if(loggedin()) {
		echo "You are already logged in.";
		require_once('end.php');
	}

	echo '<img style="float:left; width:30%; margin: 5px" src="images/artemis.jpg" alt="Greetings, Newuser!">';

	if($_POST['invitation']) $_GET['invite'] = $_POST['invitation'];
	if($_GET['invite']) {
		$sql = sprintf("SELECT * FROM Invitations WHERE id='%s'", $_GET['invite']);
		$invite = get_query($sql);
		if(!$invite) {
			echo "Unable to find an invitation for you.<BR>";
			require_once('end.php');
		}
	} else {
		//echo "New users are not currently being accepted. (find someone who can invite you)<BR>";
		//require_once('end.php');
	}
	if(!$_POST['username']) {
		echo 'Greetings, User! My name is JD, and I am here to help you sign up for your new CTNet User Account, sponsored by CyberTek Incorporated (CTI).<P>';
		echo 'If you have any questions about what is going on in the network, please visit the Help link on the menu on the right.<P>';
		echo 'To continue, please choose a name that you wish to be known by, and a password to make sure that no one else can use your name.  Please also supply an Internet email address that you can be reached at, so that we may contact you if we need to.  We will never give your contact infomration to others, or spam you.<P>';
		echo '<form name="newuser" method="post" action="'.$_SERVER['PHP_SELF'].'">
			Desired Username: <input type="text" name="username"><br>
			Desired Password: <input type="password" name="password"><br>
			Your e-mail address: <input type="text" name="email"><br>
			<input type="hidden" name="invitation" value="'.$invite['id'].'">
			<input class="submit" type="submit"></form>';
		echo "(please provide a VALID email, we promise we won't spam you, or give it out to anyone, but as the network is under development, we may need to contact you)<BR>";
	} else {
		$tempuser = get_query("SELECT * FROM Users WHERE NAME='".$_POST['username']."'");
		if($tempuser) {
			echo "Error: User name exists already.  Please try again.<BR>";
			require_once('end.php');
		}
		$user['Name'] = fix_text($_POST['username']);
		$user['Password'] = crypt($_POST['password']);
		$user['Stamina'] = 50;
		$user['Fame'] = 0.25;
		$_SESSION['username'] = $user['Name'];
		
		$sql = "SELECT StaminaPool FROM GameSettings";
		$pool = get_query($sql);
		$pool = $pool['StaminaPool'];
		$sql = "UPDATE GameSettings SET StaminaPool=".($pool - 50);
		mysql_query($sql);
		
		save_user($user, 1);
		echo "Welcome to the Network.  You are now at the network's Primary Access Point.<BR>";
		$new = get_query_array("SELECT * FROM Commands WHERE Level=".$user['Level']);
		if(sizeof($new) > 0) {
			echo "<P>New files downloaded:<P>";
			for($x = 0; $x < sizeof($new); $x++) {
				echo $new[$x]['Command'].' '.$new[$x]['Version'].'<BR>';
			}
		}
		$_SESSION['LoginTime'] = time();
		if($invite) {
			$event = sprintf("%s signed up (invited by %s)", $user['Name'], $invite['Name']);
			$u = get_user($invite['Name']);
			if($u) {
				$sql = sprintf("UPDATE Invites SET number=number+1 WHERE id=%d", $u['userid']);
				mysql_query($sql);
			}
			$sql = sprintf("DELETE FROM Invitations WHERE id='%s'", $invite['id']);
			mysql_query($sql);
		} else {
			$event = sprintf("%s signed up", $user['Name']);
		}
		$sql = sprintf("SELECT userid FROM users WHERE Name='%s'", addslashes($user['Name']));
		$id = get_query($sql);
		$id = $id['userid'];
		$user = get_user($user['Name']);
		broadcast("Please welcome $user[Name] to the Network.");
		$sql = sprintf("INSERT INTO Invites (id, number) VALUES (%d, %d)", $id, 0);
		mysql_query($sql);
		log_event($event, 0);
		error_log(date("y-m-d")." ".$user['Name']." signed up from IP ".$_SERVER['REMOTE_ADDR']." (".$_SERVER['REMOTE_HOST'].")\r\nEmail: ".$_POST['email']."\r\n", 3, "newusers.log");
		echo "Welcome, artiste %USERNAME%!<P>I have information that you may seek.  Find me within the network!<P>";
		echo "<p>Please bookmark this link to return to the game in the future:<br>";
		echo '<a href="http://ctnet.game-server.cc/">CTNet Main Page</a><br>';
		echo '<p>';
		echo '<h3>What to do next?</h3>';
		echo 'On the left hand side of your screen, you will find your statistics.  On the right hand side, you will find a clickable list of commands that you may run.<BR>';
		echo 'You might want to start with the "Post" command, and post a little bit about yourself, so those around can get to know you.  Or, you could try Read first, and see about the others.<BR>';
		echo 'After Reading and/or Posting, the Arrows on the left under the "Move" command (that ones special, its always there, its on the left) might be your next place to go.<BR>';
		echo 'If you have any problems, questions, comments, concerns, etc, please dont hesitate to use the Post or Feedback commands to make yourself heard!<BR>';
		//echo "<h1>Message of the Day</h1>";
		//echo file_get_contents("motd.html");
		display_main();
	}
	
	require_once('end.php');
?>
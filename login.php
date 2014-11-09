<?php
	
	require_once('cti.php');

	if($_POST['logout']) {
		logout("Connection dumped at user request");
		require_once('end.php');
	}
	
	if(loggedin()) {
		echo "You are already logged in.";
		echo '<form name="logout" action="'.$_SERVER['PHP_SELF'].'" method="post"><p>
			<input type="hidden" name="logout" value="true">
			<input class="submit" type="submit" value="Log Out"></form>';
		require_once('end.php');
	}
	if(!$_POST['username']) {
		echo '<form name="login" action="'.$_SERVER['PHP_SELF'].'" method="post"><p>
			<label for="username">login:</label>'.get_input_tag('username').'<br>
			<label for="password">password:</label>'.get_input_tag('password', 1).'<br>
			<input class="submit" type="submit" value="Login" '.$INPUTJS.'></form>';
	} else {
		$testuser = get_user(stripslashes($_POST['username']));
		//print_r($testuser);
		$_POST['password'] = crypt($_POST['password'], $testuser['Password']);
		//echo "comparing ".$_POST['password']." with ".$testuser['Password'];
		if( (!$testuser || !$_POST['password']) || $_POST['password'] != $testuser['Password']) {
			echo "Invalid login.";
			require_once('end.php');
		} else {
			$_SESSION['LoginTime'] = time();
			echo "Welcome, %USERNAME%.<BR>";
			$_SESSION['username'] = $testuser['Name'];
			$_SESSION['Board'] = 0;
			$_SESSION['Question'] = 0;
			$user = $testuser;
			//print_r($user);
			//echo "Last command=$user[lastcmdtime] time=".time()."<BR>";
			allocate_credits();

			if(!$user['Banned'])
			    log_event($user['Name']." logged in", $user['Level']);
			display_main();
			//echo "<h1>Message of the Day</h1>";
			//echo file_get_contents("motd.html");
			display_location_info();
			$user['Fame']+= 0.25;
			$_SESSION['Board'] = get_level_number(get_distance_from_base($user['LocX'], $user['LocY']));
		}
	}
	
	require_once('end.php');
?>
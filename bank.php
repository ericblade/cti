<?php
	$warezname = "Bank";
	$version = "1.0";
	require_once('cti.php');

	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}

	$sql = "SELECT StaminaPool FROM GameSettings";
	$pool = get_query($sql);
	$pool = $pool['StaminaPool'];
	
	switch($version) {
		case "2.0":
			$sql = sprintf("SELECT * FROM BankRob WHERE id=%d", $user['userid']);
			if(get_query($sql)) {
				echo "To avoid potential heat from the badges, this program will only hack once per day.<BR>";
				echo "There are $pool credits left in the system.<BR>";
				require_once('end.php');
			}
			if($pool < 1) {
				echo "Unable to allocate credits.<BR>";
				require_once('end.php');
			} else {
				$max = $user['Level'];
				if($user['Level'] > 256) $max = $user['Level'] * 1.5;
				if($user['Level'] > 500) $max = $user['Level'] * 2;
				if($user['Level'] > 1000) $max = $user['Level'] * 2.5;
				$r = rand($user['Level'] / 2, $max);
				if($r > $pool) $r = $pool;
				$user['Stamina'] += $r;
				$sql = "UPDATE GameSettings SET StaminaPool=".($pool - $r);
				mysql_query($sql);
				echo "$r credits transferred to your credchip.<BR>There are ".($pool - $r)." credits remaining.<BR>";
				//log_event("$user[Name] hacked the bank for $r credits", 1000);
				$sql = sprintf("INSERT INTO BankRob (id) VALUES (%d)", $user['userid']);
				mysql_query($sql);
			}
			break;
		case "1.0":
		default:
			echo "There are $pool credits left in the system.<BR>";
			break;
	}
	
	require_once('end.php');
?>
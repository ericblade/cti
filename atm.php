<?PHP
	require_once('cti.php');

	$warezname="ATM";
	$version="1.0";

	$sql = sprintf("SELECT credits FROM atms WHERE (LocX=%d AND LocY=%d)", $user['LocX'], $user['LocY']);
	
	$pool = get_query($sql);
	
	if(!$pool) {
		echo "No ATM here!<BR>";
		require_once('end.php');
	}
	$pool = $pool['credits'];
	
	$hackkey = "$user[LocX],$user[LocY]";
	//print_r($_SESSION);
	if($_SESSION['atmhack'] && array_key_exists($hackkey, $_SESSION['atmhack'])) {
		echo "You wouldn't want to get busted for repeated hacking, would you?<BR>";
		require_once('end.php');
	}
	$chance = rand(0, 99);
	$dist = get_distance_from_Base($user['LocX'], $user['LocY']);
	$catchnum = 10 + ($dist - $user['Level']);
	//echo "$catchnum $chance<BR>";
	if($chance < $catchnum) {
		echo "As you begin to setup your ATM hack, the connection suddenly closes.<BR>";
		echo "It appears that the ATM has countered your attack, and taken credits from you!<BR>";
		$user['Stamina'] -= rand(0, $user['Level'] / 2);
	} else {
		if($pool < 1) {
			echo "ATM out of credits.<BR>";
			require_once('end.php');
		}
		$max = round($dist / 3);
		if($pool < $max) $max = $pool;
		echo "Transferred $max credits to your credchip.<BR>";
		$user['Stamina'] += $max;
		$pool -= $max;
		$sql = "UPDATE atms SET Credits=$pool WHERE (LocX=$user[LocX] AND LocY=$user[LocY])";
		log_event("$user[Name] hacked an atm!", 0);
		mysql_query($sql);
	}
	
	$_SESSION['atmhack'][$hackkey] = 1;
	
	require_once('end.php');
?>
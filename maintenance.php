<?PHP
	/* This will not run on it's own, it must be included into something that includes cti.php */
	/* This would be the "midnight maintenance" module.  You will either need to include this in something that will
		get run regularly, or use some kind of scheduler to make PHP run this script when you want to do maintenance
	*/
	// TODO: Have this run through anyone logged in and idle for an hour and log them out
	$sql = "SELECT LastMaintTime FROM GameSettings";
	$lmt = get_query($sql);
	$lmt = $lmt['LastMaintTime'];
	
	$sql = "SELECT StaminaPool FROM GameSettings";
	$pool = get_query($sql);
	$pool = $pool['StaminaPool'];
	
	if( (time() - $lmt) > 86400) { // It's been more than 24 hrs since last run ..
		echo "* Running Network Maintenance<BR>";
		$sql = "UPDATE GameSettings SET LastMaintTime=".time();
		$res = mysql_query($sql);

		/*$sql = "SELECT Level FROM Users WHERE (Banned = 0 AND Level < 256)";
		$bog = get_query_array($sql);
		for($x = 0; $x < sizeof($bog); $x++) {
			$pool -= (25 + $bog[$x]['Level']);
		}
		if($pool > 0) {
			$sql = "UPDATE Users SET Stamina=(Stamina+(25 + Level)) WHERE (Banned=0 AND Level < 256)";
			$res = mysql_query($sql);
			$sql = "UPDATE GameSettings SET StaminaPool=".($pool);
			mysql_query($sql);
		} else {
			// TODO: Warn administrator that there is no more stamina available
		}*/
		
		/* ATM MAINTENANCE */
		//echo "PERFORMING ATM MAINTENANCE<BR>";
		$sql = "SELECT * FROM atms";
		$bog = get_query_array($sql);
		//echo "Found ATMS:<BR>"; print_r($bog);
		for($x = 0; $x < sizeof($bog); $x++) {
			$dist = get_distance_from_base($bog[$x]['LocX'], $bog[$x]['LocY']);
			$sql = sprintf("SELECT Credits FROM atms WHERE (LocX=%d AND LocY=%d)", $bog[$x]['LocX'], $bog[$x]['LocY']);
			$cred = get_query($sql);
			$cred = $cred['Credits'];
			if($cred > 1000) 
				continue;
			$pool -= $dist;
			if($pool > 0) {
				$sql = "UPDATE atms SET Credits=(Credits+$dist) WHERE (LocX={$bog[$x][LocX]} AND LocY={$bog[$x][LocY]})";
				//echo $sql."<BR>";
				mysql_query($sql);
			}
		}
		$sql = sprintf("UPDATE gamesettings SET StaminaPool=%d", $pool);
		mysql_query($sql);
		
		/* Clear all exhaustion */
		$sql = "UPDATE exhaust SET exhaust=0";
		mysql_query($sql);
		
		/* Clear the BankRob table */
		$sql = "DELETE FROM bankrob WHERE id>0";
		mysql_query($sql);
		
		log_event("Network maintenance run", 512);

	}
?>
	
	
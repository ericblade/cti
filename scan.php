<?PHP
	require_once('cti.php');
	
	$distpart = sprintf("SQRT( ((%d-LocX) * (%d-LocX)) + ((%d-LocY) * (%d-LocY)) ) as Distance", 
		$user['LocX'], $user['LocX'], $user['LocY'], $user['LocY']);
	if($user['Level'] < 64) $dist = 2;
	else if($user['Level'] < 128) $dist = 5;
	else if($user['Level'] < 256) $dist = 7;
	else if($user['Level'] < 512) $dist = 10;
	else if($user['Level'] < 1000) $dist = 12;
	else $dist = 15;
	$havingpart = "HAVING (distance < $dist AND distance > 0)";
	
	// Check users within range
	$sql = sprintf("SELECT 1,%s FROM Users %s ORDER BY Distance ASC", $distpart, $havingpart);
	//echo "sql=$sql<BR>";
	$users = get_query_array($sql);
	//print_r($res);
	if($users) {
		foreach($users as $u) {
			$userrange[round($u['Distance'])]++; // if we want to enumerate these we can loop thru them afterwards, i guess
			$usercount++;
		}
	}
	
	$sql = sprintf("SELECT 1,%s FROM Specials %s ORDER BY Distance ASC", $distpart, $havingpart);
	$programs = get_query_array($sql);
	if($programs) {
		foreach($programs as $p) {
			$programrange[round($u['Distance'])]++;
			$programcount++;
		}
	}
	
	/* Insert Bases when we get there */
	$sql = sprintf("SELECT 1,%s FROM Boards WHERE (LocX <>0 AND LocY <> 0) %s ORDER BY Distance ASC", $distpart, $havingpart);
	$boards = get_query_array($sql);
	if($boards) {
		foreach($boards as $b) {
			$boardrange[round($u['Distance'])]++;
			$boardcount++;
		}
	}
	
	printf("Within a range of %d nodes, there appear to be %d users, %d executeable programs, and %d boards.<BR>", $dist, $usercount, $programcount, $boardcount);

	require_once('end.php');
?>
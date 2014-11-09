<?php
	require_once('cti.php');
	
	$users = get_query_array("SELECT * FROM USERS");
	foreach($users as $u) {
		$i = round($u['Fame'] / 100);
		mysql_query("INSERT INTO Invites (id, number) VALUES (".$u['userid'].", ".$i.")");
		printf("User: %s Invites: %d<BR>", $u['Name'], $i);
	}
	require_once('end.php');
?>
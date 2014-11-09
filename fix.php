<?php
	require_once('cti.php');
	$atms = get_query_array("SELECT * FROM atms");
	foreach($atms as $atm) {
		$sql = "INSERT INTO specials (locx, locy, command, program) VALUES ($atm[LocX], $atm[LocY], 'ATM', 'atm.php')";
		mysql_query($sql);
	}
	require_once('end.php');
?>
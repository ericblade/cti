<?php
	require_once('cti.php');
	$warezname="Tell";
	$version="1.0";
	$sql = sprintf("DELETE FROM Tells WHERE (id=%d AND toid=%d)", $_GET['deletetell'], $user['userid']);
	mysql_query($sql);
	echo "Deleted.<BR>";
	require_once('end.php');
?>
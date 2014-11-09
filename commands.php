<?php
	require_once('cti.php');
	
	$sql = "SELECT DISTINCT Command, max( Version ) , max( Cost ), Program FROM Commands WHERE (Level <= ".$user['Level'].") GROUP BY Command";
	$commands = get_query_array($sql);
	echo '<table>';
	echo '<tr><th>Filename</th><th>Version</th><th>Run Cost</th><th>Description</th></tr>';
	for($x = 0; $x < sizeof($commands); $x++) {
		$sql2 = "SELECT Description FROM Commands WHERE (Command='".$commands[$x]['Command']."' and Version='".$commands[$x]['max( Version )']."')";
		$desc = get_query($sql2);
		$desc = $desc['Description'];
		echo '<tr><td>'.$commands[$x]['Command'].'</td><td>'.$commands[$x]['max( Version )'].'</td>';
		echo '<td>'.$commands[$x]['max( Cost )'].'</td>';
		echo '<td>'.$desc.'</td></tr>';
	}
	echo '</table>';
	$_SESSION['command'] = "";
	require_once('end.php');
?>
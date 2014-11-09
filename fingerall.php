<?php
	require_once('cti.php');

	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}
	
	// TODO: Implement an Admin flag in the Users table, and let admin's see banned peoples
	// TODO: also get value fudging like Finger should have
	// TODO: maybe make the cost of it be dependant on how many people it returns?
	// TODO: Also have it check only with zone, or a certain range? (Zones not yet implemented)
	$u = get_query_array("SELECT * FROM Users"); //WHERE Banned=0");
	echo '<table><tr>';
	echo '<th>Name</th>';
	echo '<th>Access Level</th>';
	echo '<th>User Group</th>';
	echo '<th>Credits</th>';
	switch($version) {
		case "2.0":
			echo '<th>Location</th>';
			break;
		case "1.0":
		default:
			echo '<th>Distance</th>';
			break;
	}
	echo '<th>ComLev</th>';
	//echo '<th>Fame</th>';
	echo "</tr>";
	foreach($u as $v) {
		if($v['Banned']) {
			$v['Stamina'] = "**";
			$v['LocX'] = "**";
			$v['LocY'] = "**";
			$v['Name'] .= " *Banned*";
		}
		echo "<tr><td>$v[Name]</td>";
		echo "<td>";
		if($user['Level'] > $v['Level']) {
			echo "($v[Level]) ";
		}
		echo get_level_title($v['Level']);
		echo "</td>";
		if($v['GroupID'] > 0) {
			$vgroup = load_group($v['GroupID']);
			printf("<td>%s</td>", $vgroup['Name']);
		} else {
			echo '<td></td>';
		}
		echo "<td>$v[Stamina]</td>";
		switch($version) {
			case "2.0":
				echo "<td>($v[LocX],$v[LocY])</td>";
				break;
			case "1.0":
			default:
				if($v['Banned'])
					echo '<td>**</td>';
				else
					echo '<td>'.get_distance_from_point($v['LocX'],$v['LocY'],$user['LocX'],$user['LocY']).'</td>';
				break;
		}
		echo "<td>".get_fame_title($v[Fame],$v[Level])."</td>";
//		echo '<td>';
//		if( $v['Level'] < 0) {
//			echo '**';
//		} else {
//			echo sprintf("%02.02f", (($v['Fame'] / 2) / ($v['Level']+1) * 100));
//		}
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
	$_SESSION['command'] = "";
	require_once('end.php');
?>
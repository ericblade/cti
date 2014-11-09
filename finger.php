<?PHP
	require_once('cti.php');

	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}
	
	if(!$_POST['name']) {
		user_select_form(0);
		require_once('end.php');
	} else {
		$u = get_user($_POST['name']);
		if(!$u) {
			echo "User '$_POST[name]' not found.<BR>";
			require_once('end.php');
		}
		// TODO: Get some kind of template here
		// TODO: Implement First Login and Last Login in User table
		// TODO: Implement skewing if you are significantly below the target's level .. possibly make that an option some people have?
		// TODO: Implement a template function that takes a filename, and a mapping of strings to replace, and returns the whole kit n caboodle
		echo '<fieldset><legend>Information for User '.$u['Name'].'</legend>';
		echo get_user_menu($u['Name']);
		echo '<table>';
		printf('<tr><th style="width:100px; text-align: right;">User:</th><td>%s', $u['Name']);
		//echo '<tr><th>User:</th><td>'.$u['Name'];
		if($u['Banned']) echo ' *Banned*';
		echo '</td></tr>';
		echo '<tr><th style="width:100px; text-align: right;">Access Level:</th><td>'.get_level_title($u['Level']).'</td></tr>';
		if($user['Level'] > $u['Level']) {
			echo '<tr><th style="width:100px; text-align: right;">Actual Level:</th><td>'.$u['Level'].'</td></tr>';
		}
		echo '<tr><th style="width:100px; text-align: right;">Credits:</th><td>'.$u['Stamina'].'</td></tr>';
		//if($u['GroupID'] > 0) {
			$ugroup = load_group($u['GroupID']);
			echo '<tr><th style="width: 100px; text-align: right;">User Group:</th><td>'.$ugroup['Name'].'</td></tr>';
		//}
		
		// TODO: Version 3, display "elementals" (skills?) 
		switch($version) {
			case "2.01":
				echo '<tr><th style="width:100px; text-align: right;">Location:</th><td>('.$u['LocX'].','.$u['LocY'].')</td></tr>';
				if($u['Level'] < 256) {
					$alloc = (25 + $u['Level']);
					echo '<tr><th style="width:100px; text-align: right;">Credit Allocation:</th><td>'.$alloc.'/day</td>';
				}
				echo '<tr><th style="width:100px; text-align: right;">Credchip Max:</th><td>'.(100 + ($u['Level'] * 50)).'</td></tr>';
				echo '<tr><th style="width:100px; text-align: right;">ComLev:</th><td>';
				$maxlev = round($u['Fame'] / 2);
				echo sprintf("%02.02f (%d)", (($u['Fame'] / 2) / ($u['Level']+1) * 100), $maxlev);
				echo '</td>';

				break;
			case "1.01":
			default:
				echo '<tr><th style="width:100px; text-align: right;">ComLev:</th><td>';
				echo sprintf("%02.02f", (($u['Fame'] / 2) / ($u['Level']+1) * 100));
				echo '</td>';
				echo '<tr><th style="width:100px; text-align: right;">Distance:</th><td>'.get_distance_from_point($u['LocX'], $u['LocY'], $user['LocX'], $user['LocY']).'</td></tr>';
				break;
		}
		echo '<tr>ComLev<th>:</th><td>'.get_fame_title($u['Fame'], $u['Level']).'</td></tr>';
		echo '</table></fieldset>';
		$_SESSION['command'] = "";
	}
	require_once('end.php');
?>
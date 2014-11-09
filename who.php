<?PHP
	// TODO: 2.0, tells what the last command executed was?
	require_once('cti.php');
	
	$sql = sprintf("SELECT * FROM Users WHERE lastcmdtime > %d", time() - (3600)); // last hour
	$users = get_query_array($sql);
	if($users) {
		echo '<table><tr><th style="text-align:left">User</th><th style="text-align:left">Last Activity</th></tr>';
		foreach($users as $u) {
			printf("<tr><td>%s</td><td>%s</td>", $u['Name'], mydate((int)$u['lastcmdtime']));
		}
		echo '</tr></table>';
	} else {
		echo "No one logged in.<BR>";
	}
	require_once('end.php');
?>
<?php
	$warezname="Help";
	$version="1.0";
	
	require_once('cti.php');
	
	if(!$_GET['num']) {
		$sql = sprintf("SELECT * FROM Messages WHERE (boardid = -2 and (deleted is null or deleted = 0 or sender='%s') and level <= %d) ORDER BY Level",
						addslashes($user['Name']), $user['Level']);
		//$sql = "SELECT * FROM Messages WHERE (boardid = -2 and (deleted is null or deleted = 0 or sender='".$user['Name']."') and level <= ".$user['Level'].") ORDER BY Level";
		$helps = get_query_array($sql);

		if(!$helps) {
			echo "No help data found.<BR>";
			$_SESSION['command'] = "";
		} else {
			echo '<table>';
			echo '<tr><th>Level</th><th style="text-align:left;">Help File</th><th style="text-align:left;">Author</th></tr>';
			
			foreach($helps as $h) {
				$link = '<a href="'.$_SERVER['PHPSELF'].'?num='.$h['msgid'].'">';
				printf("<tr><td style='text-align:center;'>%d</td><td>%s%s</a></td><td>%s</td></tr>", $h['level'], $link, $h['subject'], $h['sender']);
			}
			echo '</table>';
		}
	} else {
		$sql = "SELECT * FROM Messages WHERE (msgid = ".$_GET['num']." AND boardid = -2 and level <= ".$user['Level']." and (deleted is null or deleted = 0))";
		$h = get_query($sql);
		if(!$h) {
			echo "Unable to find help information.<BR>";
			$_SESSION['command'] = "";
		}
		echo "<h1>$h[subject]</h1>";
		echo "<address>by $h[sender]</address>";
		echo "<p>$h[msgtext]</p>";
		$_SESSION['command'] = "";
	}
	require_once('end.php');
?>
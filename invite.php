<?PHP
	require_once('cti.php');
	
	$sql = sprintf("SELECT number FROM invites WHERE id=%d", $user['userid']);
	$count = get_query($sql);
	$count = $count['number'];
	
	if($_POST['getnew']) {
		if($count < 1) {
			echo "You don't have any invitations.<BR>";
			require_once('end.php');
		}

		echo "Here is an invitation link for you, give it to someone:<BR>";
		$id = md5(uniqid(rand(),1));
		$link = 'http://ctnet.game-server.cc/newuser.php?invite='.$id;
		echo '<a href="'.$link.'">'.$link.'</a><p>';
		$sql = sprintf("UPDATE invites SET number=number-1 WHERE id=%d", $user['userid']);
		mysql_query($sql);
		$sql = sprintf("INSERT INTO Invitations (id, Name, ident) VALUES ('%s', '%s', '%s')", $id, $user['Name'], $_POST['ident']);
		mysql_query($sql);
		echo "You have ".($count-1)." invitations left.<BR>";
		$_SESSION['command'] = "";
	} else {
		$i = get_query_array(sprintf("SELECT * FROM invitations WHERE NAME='%s'", $user['Name']));
		//print_r($i);
		if(!$i) {
			echo "No pending invites found.<BR>";
		} else {
			echo "<table>";
			foreach($i as $inv) {
				printf('<tr><td><a href="http://ctnet.game-server.cc/newuser.php?invite=%s">Invite Link</a></td><td>%s</td></tr>', 
					$inv['id'], $inv['ident']);
			}
			echo '</table>';
			echo "<br>Copy a link, and give it to a friend.<BR>";
		}
		echo '<form name="invite" method="post" action="'.$_SERVER['PHP_SELF'].'"><p>';
		echo '<input type="hidden" name="getnew" value="1">';
		echo 'Enter a Name to identify an invitation by: <input type="text" class="text" name="ident">';
		echo '<input type="submit" value="Get New Invitation Code">';
		echo '</form>';
	}
	require_once('end.php');
?>
<?PHP
	require_once('cti.php');
	
	if(!loggedin() || $version < 1) {
		echo "You do not have access to that.<BR>";
		require_once('end.php');
	}
	if(!$_POST['boardname']) {
		echo '<fieldset><legend>'.$warezname.' ' . $version.'</legend>';
		echo '<form name="createboard" action="'.$_SERVER['PHPSELF'].'" method="post"><p>';
		echo '<label for="boardname">Board Name:</label><input type="text" name="boardname" '.$INPUTJS.'><br>';
		echo '<label for="description">Description:</label><input type="text" name="description" '.$INPUTJS.'><br>';
		if($version > 1) {
			echo '<label for="level">Access Level:</label><input type="text" name="level" '.$INPUTJS.'><br>';
		}
		if($version > 2) {
			echo '* Use 0,0 to make accessable from anywhere.<br>';
			echo 'Location: <label for="locx">X:</label><input type="text" name="locx" value="0"><label for="locy">Y:</label><input type="text" name="locy" value="0"><br>';
		}
		echo '<input class="submit" type="submit" value="Create" '.$INPUTJS.'>';
		echo '</form>';
		echo '</fieldset>';
	} else {
		$_POST['boardname'] = fix_text($_POST['boardname']);
		$sql = sprintf("SELECT count(*) as c FROM Boards Where boardname='%s'", $_POST['boardname']);
		$res = get_query($sql);
		$res = $res['c'];
		if($res) {
			echo "There seems to already be a board by this name.  Let's try this again.<BR>";
			require_once('end.php');
		}
		if($version < 2) $_POST['level'] = 1;
		if($version < 3) {
			$_POST['locx'] = 0;
			$_POST['locy'] = 0;
		}
		$sql = sprintf("INSERT INTO Boards (boardname, description, creator, level, locx, locy) VALUES ('%s', '%s', %d, %d, %d, %d)",
				$_POST['boardname'], $_POST['description'], $user['userid'], $_POST['level'], $_POST['locx'], $_POST['locy']);
			
		//echo "create sql=$sql<BR>";
		$res = mysql_query($sql);
		if(!$res) {
			echo "Error creating board.<BR>";
		} else {
			$_SESSION['command'] = "";
			echo "New board created.<BR>";
			log_event($user['Name'] . " created a new board, ".$_POST['boardname'].".", $_POST['level']);
			$user['Fame'] += 5;
		}
	}
	
	require_once('end.php');
?>
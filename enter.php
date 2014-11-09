<?PHP
	$warezname="Enter";
	$version="1.0";
	
	require_once('cti.php');

	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}
	
	if(!$_POST['msgtext']) {
		//if(!$_SESSION['Board']) $_SESSION['Board'] = "0";

		if($_POST['subject']) {
			if(substr($_POST['subject'], 0, 3) != "Re:") $_POST['subject'] = "Re:".$_POST['subject'];
		}
		?>
		<fieldset>
			<legend>Create Help File</legend>
			<form name="sendmessage" method="post" action="<?PHP echo $_SERVER['PHP_SELF']; ?>"><p>
				<label for="level">Level:</label><input type="text" name="level" maxlength="4" size="4" value="<?PHP echo $user['Level']; ?>"><br>
				<label for="subject">Subj:</label><input type="text" name="subject" maxlength="61" size="32"><br>
				<br>
				<input type="hidden" name="board" value="-2">
				<label for="msgtext">Text:</label><textarea name="msgtext" rows="10"></textarea><p>
				<input class="submit" type="submit" value="Enter">
			</form>
		</fieldset>
		<?PHP
		if($_POST['parentid']) {
			$sql = "SELECT * FROM Messages WHERE (msgid={$_POST[parentid]} AND boardid=-2)";
			$msg = get_query($sql);
			echo '<p><div style="padding: 2px;">Original Message:<br>'.$msg['msgtext'].'</div>';
		}
	} else {
		if(!$_POST['parentid']) $_POST['parentid'] = "0"; // force it to string 0 so it still is there even if it's numeric 0, etc
		$_POST['msgtext'] = fix_text($_POST['msgtext']);
		$sql = "INSERT INTO Messages (Level,Parentid,Sender,Timesent,Subject,BoardID,MsgText) VALUES (".$_POST['level'].",".$_POST['parentid'].", '".$user['Name']."', '".date('Y-m-d H:i:s')."','".$_POST['subject']."',-2,'".$_POST['msgtext']."')";
		$res = mysql_query($sql);
		if(!$res) echo "error: $sql = $res";
		else {
			echo "Message posted.";
			$fame = rate_text($_POST['msgtext']);
			$user['Fame'] += $fame;
			//echo $fame."<BR>";
		}
		echo '<form name="return" method="post" action="read.php"><p>
			<input class="submit" type="submit" value="Return">
			</form>';
		$_SESSION['command'] = "";
	}
	
	require_once('end.php');
	
	function rate_text($text) {
		global $user;
		//echo "rt($text)<BR>";
		$base = 100;
		
		if(strtoupper($text) == $text) 
			$base = -100;
		if(strtolower($text) == $text)
			$base -= 20;
		$text = preg_replace('/\s\s+/', ' ', $text);
		//echo "pr text=$text<BR>";
		for($x = 0; $x < strlen($text); $x++) {
			switch($text[$x]) {
				case '.':
				case '!':
				case '?':
					$sc++;
					if($punc) $repeatedpunc++;
					$punc = 1;
					break;
				case ' ': 
					$wc++;
				default:
					$punc = 0;
					break;
			}
		}
		$base -= $repeatedpunc;
		error_log($user['Name']." message: $text wc=$wc sc=$sc base=$base act=".$base / ($user['Level']+1)."\r\n", 3, "fame.log");
		if($sc > 10) $base += 10;
		if($wc > 50) $base += 10;
		if( $wc && $sc && (($wc / $sc) > 5)) $base += 10;
		if(!$sc) $base -= 10; // no sentences!
	
		if(strstr($text, "unilaterally")) {
			echo "It seems you've said a magic word.<BR>";
			$base += 20;
		}
		return $base / ($user['Level']+1);
	}
	require_once('end.php');
?>
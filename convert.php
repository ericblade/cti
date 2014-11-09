<?PHP
	// This converts "Trivia Bot" scripts from http://www.imatowns.com/xelagot/xlgtopictrivia.html into our pending database
	require_once('cti.php');
	if(!$_POST['text']) {
		echo "<form name=convert method=post action=$_SERVER[PHP_SELF]><p>";
		echo "Input text:<br><textarea name=text></textarea>";
		echo "<input type=submit value=submit>";
	} else {
		$new = explode("\r\n", $_POST['text']);
		foreach($new as $line) {
			$x = explode("//", $line);
			
			$question = $x[sizeof($x)-1];
			$x = explode("/", $x[0]);
			
			$cat = $x[1];
			$a1 = $x[2];
			$a2 = $x[3];
			$a3 = $x[4];
			$a4 = $x[5];
			
			$sql = sprintf("INSERT INTO pendingquestions ".
						"(question, answer1, answer2, answer3, answer4, submitter, citation, level, category) VALUES ".
						"('%s', '%s', '%s', '%s', '%s', %d, '%s', %d, '%s')",
						$question, $a1, $a2, $a3,
						$a4, $user['userid'], "", $user['Level'], "Other");
			mysql_query($sql);
		}
	}
	require_once('end.php');
?>
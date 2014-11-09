<?PHP
	$warezname="MOTD";
	$version="1.0";
	require_once('cti.php');

	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}
	
	if(!$_POST['text']) {
		echo "Current MOTD:<BR>";
		echo file_get_contents("motd.html");
		echo '<br>';
		echo '<form name="motd" method="post" action="'.$_SERVER['PHPSELF'].'">';
		
		if($version == "1.0")
			echo '<p>Enter text to add to top:<br>';
		else
			echo '<p>Enter new MOTD:<br>';
			
		echo '<textarea rows="10" name="text">';
		if($version == "2.0")
			echo strip_tags(file_get_contents("motd.html"));
		echo '</textarea>';
		echo '<br><input class="submit" type="submit" name="Add"></form>';
	} else {
		$_SESSION['command'] = "";
		if($version == "2.0") {
			$new = fix_text($_POST['text']);
			unlink("motd.html");
		} else {
			$new = fix_text($_POST['text'] . "<P>" . file_get_contents("motd.html"));
		}
		file_put_contents("motd.html", $new);
		echo "New MOTD:<br>";
		echo file_get_contents("motd.html");
		echo '<br>';
	}
	require_once('end.php');
?>
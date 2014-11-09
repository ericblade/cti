<?php
	require_once('cti.php');
	if(!loggedin() || $version < 1) {
		echo "You don't have access to run that.<BR>(did you get logged out? run out of credit? time?)<br>";
		require_once('end.php');
	}

	// TODO: We need to move this to a pending database
	if(!$_POST['question'] || !$_POST['answer1']) {
?>
		<fieldset>
			<legend><?PHP echo "$warezname $version"; ?></legend>
		<form name="submit" method="post" action="<?PHP echo $_SERVER['PHP_SELF']; ?>">
			<p>
			<label for="category">Category:</label>
			<select name="category">
				<option>Other
				<option>Sports
				<option>Music
				<option>History
				<option>Geography
				<option>Science/Tech
				<option>Weird
				<option>TV
				<option>Movies
				<option>Literature
			</select>
			<br>
			<label for="question">Question:</label><input type="text" class="text" name="question"><br>
			<label for="answer1">1st Answer:</label><input type="text" class="text" name="answer1"><br>
			<label for="answer2">2nd Answer:</label><input type="text" class="text" name="answer2"><br>
			<label for="answer3">3rd Answer:</label><input type="text" class="text" name="answer3"><br>
			<label for="answer4">4th Answer:</label><input type="text" class="text" name="answer4"><br>
			<label for="citation">Citation:</label><input type="text" class="text" name="citation"><br>
			If Citation is an Internet site, please make sure it fits within the space allowed, otherwise use <a href="http://www.tinyurl.com/" target="_blank">TinyUrl</a> to make it shorter.<BR>
			<p><input class="submit" type="submit" value="Submit">
		</form>
<?PHP
	} else {
		$sql = sprintf("INSERT INTO pendingquestions ".
						"(question, answer1, answer2, answer3, answer4, submitter, citation, level, category) VALUES ".
						"('%s', '%s', '%s', '%s', '%s', %d, '%s', %d, '%s')",
						$_POST['question'], $_POST['answer1'], $_POST['answer2'], $_POST['answer3'],
						$_POST['answer4'], $user['userid'], $_POST['citation'], $user['Level']+100, $_POST['category']);
		mysql_query($sql);
		echo "New password pending.<BR>";
		$_SESSION['command'] = "";
	}
	require_once('end.php');
?>

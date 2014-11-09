<?PHP
	require_once('cti.php');
	
	//print_r($_POST);
	if($_POST['decline']) {
		$res = mysql_query("DELETE FROM pendingquestions WHERE id=".$_POST['id']);
		if($res) {
			echo "Question declined.<BR>";
		} else {
			echo "Error deleting.<BR>";
		}
	}
	if($_POST['approve']) {
		$q = get_query("SELECT * FROM pendingquestions WHERE id=".$_POST['id']);
		$sql = sprintf("INSERT INTO questions ".
						"(question, answer1, answer2, answer3, answer4, submitter, level, category) VALUES ".
						"('%s', '%s', '%s', '%s', '%s', %d, %d, '%s')",
						addslashes($q['question']), addslashes($q['answer1']), addslashes($q['answer2']), addslashes($q['answer3']),
						$q['answer4'], $q['submitter'], $q['level'], $q['category']);
		
		$res = mysql_query($sql);
		if($res) {
			echo "Question approved.<BR>";
			$res = mysql_query("DELETE FROM pendingquestions WHERE id=".$_POST['id']);
		} else {
			echo "Error approving question.<BR>";
		}
	}
	if($_POST['edit']) {
		$q = get_query("SELECT * FROM pendingquestions WHERE id=".$_POST['id']);
		?>
		<form name="submit" method="post" action="submit.php">
			<p>
			<label for="category">Category:
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
			</label><br>
			<label for="question">Question:<input type="text" class="text" name="question" value="<?PHP echo $q[question]; ?>"></label><br>
			<label for="answer1">1st Answer:<input type="text" class="text" name="answer1" value="<?PHP echo $q[answer1]; ?>"></label><br>
			<label for="answer2">2nd Answer:<input type="text" class="text" name="answer2" value="<?PHP echo $q[answer2]; ?>"></label><br>
			<label for="answer3">3rd Answer:<input type="text" class="text" name="answer3" value="<?PHP echo $q[answer3]; ?>"></label><br>
			<label for="answer4">4th Answer:<input type="text" class="text" name="answer4" value="<?PHP echo $q[answer4]; ?>"></label><br>
			<label for="citation">Citation:<input type="text" class="text" name="citation" value="<?PHP echo $q[citation]; ?>"></label><br>
			If Citation is an Internet site, please make sure it fits within the space allowed, otherwise use <a href="http://www.tinyurl.com/" target="_blank">TinyUrl</a> to make it shorter.<BR>
			<p><input class="submit" type="submit" value="Submit">
		</form>
		<?PHP
	}
	
	$pending = get_query_array("SELECT * FROM pendingquestions");

	if(!$pending) {
		echo "No pending questions!<BR>";
		require_once('end.php');
	} else {
?>
		<table>
			<tr>
				<th>Category</th><th>Question</th><th>A1</th><th>A2</th><th>A3</th><th>A4</th><th>Citation</th><th>Level</th><th>Submitter</th>
			</tr>
<?PHP
			foreach($pending as $p) {
				echo "<tr>";
				echo "<td>$p[category]</td><td>$p[question]</td><td>$p[answer1]</td><td>$p[answer2]</td><td>$p[answer3]</td><td>$p[answer4]</td><td>$p[citation]</td><td>$p[level]</td>";
				$u = get_user((int)$p[submitter]);
				echo "<td>$u[Name]</td>";
				echo "</tr>";
				echo "<tr><td colspan=9><form name=approve action=$_SERVER[PHP_SELF] method=POST><p><input type=hidden name=id value=$p[id]><input type=submit name=approve value=Approve><input type=submit name=decline value=Decline><input type=submit name=edit value=Edit></form></td></tr>";
			}
		echo '</table>';
		//print_r($p);
		//print_r($u);
	}
	require_once('end.php');
?>
				
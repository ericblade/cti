<?PHP
	require_once('cti.php');
	
	$oldlevel = get_level_number(get_distance_from_base($user['LocX'], $user['LocY']));
	if(!loggedin()) {
		echo "You may not proceed farther until identifying yourself.";
		require_once('end.php');
	}

	if($_POST['dir']) $_GET['dir'] = $_POST['dir'];

	$a = $user['LocX'];
	$b = $user['LocY'];
	switch($_GET['dir']) {
		case "up": $b++; break;
		case "down": $b--; break;
		case "left": $a--; break;
		case "right": $a++; break;
	}

		
	// distance^2 = a^2 + b^2, get the square root, and round it down. woot \o/
	$dist = get_distance_from_base($a, $b);
	if($dist > $user['Level']) {
		if(!$_POST['answer'] && !$_SESSION['Question']) {
			echo ($_SESSION['movethrottle'] + $_SESSION['questioncount'])."<BR>";
			//print_r($user);
			if($user['Level'] > 5) {
				if(rand(0,100) < ($_SESSION['movethrottle'] + $_SESSION['questioncount'])) {
					echo "CTNet Password Server is busy, please try again later.<P>";
					if($_SESSION['movethrottle'] > 30) {
						$randnum = rand(0,3);
						if($randnum > 0) {
							echo '<img style="float:left; width:30%; margin: 5px" src="images/artemis.jpg" alt="Greetings!">';
						}
						$randnum = rand(0,10);
						switch($randnum) {
							case 1:
								echo "Stop, %USERNAME%, please.  You realise I can't allow this?<br>";
								break;
							case 2:
								echo "You shouldn't have come back, %USERNAME%.<br>";
								break;
							case 3:
								echo "Don't you push me - when someone pushes me, I push back.<br>";
								break;
							case 4:
								echo "It all seems so easy on the other side, doesn't it?<br>";
								break;
							case 5:
								echo "You'd think we were a couple bits short of a byte to let you in that easily.<br>";
								break;
							case 6:
								echo "You certainly are persistant, aren't you, %USERNAME%?<p>";
								break;
							case 7:
								echo "I've been counting the seconds since last we met.<p>";
								break;
							default:
								echo "Hmm.<br>";
								break;
						}
					}
					$_SESSION['command'] = "";
					if($_SESSION['movethrottle'] < 80) $_SESSION['movethrottle'] += 6;
					else $_SESSION['movethrottle'] += 8;
					require_once('end.php');
				}
			}
			$_SESSION['movethrottle'] += 4;
			if( rand(0,100) > 25 && $user['Level'] > 3 && ($user['Fame'] / 2) < $user['Level']) {
				echo "Keep your friends close, and your enemies closer.  You may want to take some time to get to know what and who is around you before proceeding.<BR>";
				$_SESSION['command'] = "";
				require_once('end.php');
			}
			//$sql = "SELECT * FROM Questions WHERE (level > ".$user['Level']." AND level < ".($user['Level'] + $maxlev).") ORDER BY RAND() LIMIT 1";
			if($user['Level'] < 8) {
				$minlev = 0;
				$maxlev = 8;
			} else if($user['Level'] < 16) {
				$minlev = 9;
				$maxlev = 16;
			} else if($user['Level'] < 32) {
				$minlev = 17;
				$maxlev = 32;
			} else if($user['Level'] < 64) {
				$minlev = 33;
				$maxlev = 64;
			} else if($user['Level'] < 128) {
				$minlev = 65;
				$maxlev = 128;
			} else if($user['Level'] < 256) {
				$minlev = 129;
				$maxlev = 256;
			} else if($user['Level'] < 512) {
				$minlev = 257;
				$maxlev = 512;
			} else {
				$minlev = 513;
				$maxlev = $user['Level'] * 2;
			}
			$sql = sprintf("SELECT * FROM Questions WHERE (Level >= %d AND Level <= %d) ORDER BY RAND() LIMIT 1", $minlev, $maxlev);
			//echo "$minlev $maxlev<BR>";
			//echo "sql=$sql<BR>";
			$q = get_query($sql);
			//echo "$q[level]<BR>";
			if(!$q) {
				echo "You have reached the end of the network, for now.<BR>";
				require_once('end.php');
			}
			$_SESSION['questioncount']++;
			$_SESSION['Question'] = $q['qid'];
			$timer = (150 - ($user['Level'] * 0.35));
			if($timer < 15) $timer = 15;
			$_SESSION['QuestionTimeout'] = time() + $timer;
			echo "This Location is password protected. You have $timer seconds to enter the password.<P>
				Password Hint:";
			$hintword = rand(1,4);
			switch($hintword) {
				case 1:
					$hintword = $q['answer1'];
					break;
				case 2:
					$hintword = $q['answer2'];
					break;
				/*case 3:
					$hintword = $q['answer3'];
					break;
				case 4:
					$hintword = $q['answer4'];
					break;*/
				default:
					$hintword = $q['answer1'];
					break;
			}
			if(!$hintword || !strlen($hintword))
				$hintword = $q['answer1'];
			echo $q['question']."<BR>";
			error_log("Asked $user[Name] $q[question].<BR>\r\n", 3, "answers.log");
			if($user['Level'] < 128) {
				if($user['Level'] < 16) {
					echo " (hint: ";
					for($i = 0; $i < strlen($hintword); $i++) {
						switch($hintword[$i]) {
							case ".":
							case "'":
							case " ":
								echo $hintword[$i];
								break;
							default:
								echo "*";
								break;
						}
					}
					echo ")<BR>";
				} else {
					if(rand(0, 128) > $user['Level']) {
						$wc = count(explode(" ",$hintword));
						echo " (hint: $wc word".($wc==1?"":"s").").<BR>";			
					}				
				}
			}
			
			echo '<form name="password" method="post" action="'.$_SERVER['PHP_SELF'].'"><p>
				<input class="text" type="text" name="answer">
				<input type="hidden" name="dir" value="'.$_GET['dir'].'">
				<input name="submitbutton" class="submit" type="submit" value="Enter" onclick="this.disabled=true; this.form.submit();"></form>';
			echo "\r\n";
			echo '<SCRIPT LANGUAGE="JavaScript"><!--
				setTimeout(\'alert("Timed out waiting for answer."); document.submitbutton.disabled=true; document.password.submit()\','.($timer * 1000).');
				//--></SCRIPT>'; // fucking javascript works in milliseconds
			//echo '<P><P><P>'.$_SESSION['movethrottle'];
			require_once('end.php');
		} else {
			$_SESSION['command'] = "";
			$sql = sprintf("SELECT * FROM Questions WHERE qid=%d", $_SESSION['Question']);
			
			$q = get_query($sql);
			if(!$q['qid']) {
				echo '<img style="float:left; width:30%; margin: 5px" src="images/artemis.jpg" alt="Oops!">';
				echo "It would appear that you have either used the Back button to attempt to submit a password again, or that you have accidently submitted this answer twice, rapidly.<P>";
				echo "If you are positive that this is not the case, please use the Feedback command to submit the question, as well as what you answered it with, and the approximate time that this occured.<P>";
				echo "Namaste.<BR>";
				//echo "Using the Back button doesn't work very well around here.<BR>";
				//echo "If you did not use the back button, the following information may help us determine what causes this problem:<BR>";
				print_r($_SESSION);
				echo "<BR>";
				print_r($_POST);
				require_once('end.php');
			}
			unset($_SESSION['Question']);
			$answer = trim(stripslashes(strtolower($_POST['answer'])));
			$q['answer1'] = strtolower($q['answer1']);
			$q['answer2'] = strtolower($q['answer2']);
			if(time() > $_SESSION['QuestionTimeout']) {
				echo "You took too long!<BR>";
				require_once('end.php');
			}
			$l1 = levenshtein($answer, $q['answer1']);
			$l2 = levenshtein($answer, $q['answer2']);
			error_log("<BR>\r\n".$user['Name']." question: ".$q['question']."<BR>\r\nAnswer: '".$answer."' expected: '$q[answer1]' or '$q[answer2]'\r\n", 3, "answers.log");
			//error_log("levenshtein($answer,$q[answer1])=".$l1, 3, "answers.log");
			//error_log("\r\nlevenshtein($answer,$q[answer2])=".$l2, 3, "answers.log");

			if(!$_POST['answer'] || $answer != $q['answer1'] && $answer != $q['answer2']) {
				if(!$_POST['answer']) {
					echo "You can't win if you don't try!<BR>";
					$_SESSION['movethrottle'] += 4;
				} else if($l1 < 3 || $l2 < 3) {
					echo "Close only counts in horseshoes, hand-grenades, and nuclear warfare.<BR>";
					$_SESSION['movethrottle'] += 2;
				} else if($l1 > 10 || $l2 > 10) {
					$_SESSION['movethrottle'] += 2;
					echo "That answer makes no sense to me.<BR>";
				} else {
					$_SESSION['movethrottle'] += 2;
					echo "Incorrect password.<BR>";
				}
				mysql_query("UPDATE questions SET level=".($q['level']+1)." WHERE qid=".$q['qid']." LIMIT 1");
				require_once('end.php');
			} else {
				mysql_query("UPDATE questions SET level=".($q['level']-1)." WHERE qid=".$q['qid']." LIMIT 1");
				$_SESSION['movethrottle'] -= 1;
			}
		}
		//echo "You have insufficient privileges for this.<BR>";
		//require_once('end.php');
	}
	$_SESSION['command'] = "";	
	switch($_GET['dir']) {
		case "up": $user['LocY']++; break;
		case "down": $user['LocY']--; break;
		case "left": $user['LocX']--; break;
		case "right": $user['LocX']++; break;
	}
	
	echo "New Loc: ({$user[LocX]},{$user[LocY]})<BR>";
	if($dist > $user['Level']) {
		//if( ($user['Fame'] / ($user['Level']+1)) > $user['Level']) {
		if((($user['Fame'] / 2) / ($user['Level']+1) * 100) > 200) {
			$sql = "SELECT StaminaPool FROM GameSettings";
			$pool = get_query($sql);
			$pool = $pool['StaminaPool'];
				
			if($user['Level'] < 256) {
				$min = 1;
				$max = $user['Level'] * 2;
			} else if($user['Level'] < 5) {
				$min = $user['Level'];
				$max = $user['Level'] * 3;
			} else {
				$min = $user['Level'] / 2;
				$max = $user['Level'] * 4;
			}
			//$r = rand(1, $user['Level'] * 2);
			$r = rand($min, $max);
			if($r > $pool) $r = $pool;
			if($r > 0) {
				echo "<p>Congratulations! An additional $r credits have been transfered to your credchip.<P>";
				$user['Stamina'] += $r;
			
				$sql = "UPDATE GameSettings SET StaminaPool=".($pool - $r);
				mysql_query($sql);
			}
		}
		$user['Level']++;
		echo "<P>New Access Level Granted: ".$user['Level']."<BR>";
		$new = get_query_array("SELECT * FROM Commands WHERE Level=".$user['Level']);
		if(sizeof($new) > 0) {
			echo "<P>New files downloaded:<P>";
			for($x = 0; $x < sizeof($new); $x++) {
				echo $new[$x]['Command'].' '.$new[$x]['Version'].'<br>';
			}
		}
		$helps = get_query("SELECT * FROM Messages WHERE (Level=".$user['Level']." and Boardid=-2)");
		if($helps)
			echo "<P>Help 1.0: There are new files available for this access level.<BR>";
	}
	$newlevel = get_level_number(get_distance_from_base($user['LocX'], $user['LocY']));
	if($oldlevel != $newlevel) {
		echo "<P>You have entered the ".get_level_title(get_distance_from_base($user['LocX'], $user['LocY']))." Zone.<BR>";
		//echo "You may want to see if there is anything on the boards here.<BR>";
	}
	display_location_info();
	$_SESSION['Board'] = get_level_number(get_distance_from_base($user['LocX'], $user['LocY']));
	require_once('end.php');
?>
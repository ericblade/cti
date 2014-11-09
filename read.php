<?php

	require_once 'cti.php';

	//echo "Board=".$_SESSION['Board']."<BR>";
	//$_SESSION['Board'] = get_level_number(get_distance_from_base($user['LocX'], $user['LocY']));
	if(!$_SESSION['Board']) $_SESSION['Board'] = "0";
	$sql = sprintf("SELECT * FROM boards WHERE boardid=%d", $_SESSION['Board']);
	$board = get_query($sql);
	/*if($_SESSION['Board'] < 0) {
		$_SESSION['Board'] = $_SESSION['OldBoard'];
		unset($_SESSION['OldBoard']);
	}*/
		
	//echo '<form name="newmessage" action="post.php" method="post">';
	//echo '<p>';
	//echo '<input class="submit" type="submit" value="Post New Message">';
	//echo '</form>';
	if(!$_GET['start']) $_GET['start'] = "0";
	if($_GET['motd']) $_SESSION['Board'] = -3;
	if(!$_GET['msgid']) {
		//TODO: boardid usage
		$lastread = get_last_read($user['userid'], $_SESSION['Board']);
		switch($version) {
			case "1.0":
				$sql = sprintf("SELECT * FROM Messages WHERE (boardid = %d and (deleted is null or deleted = 0 or sender='%s')) ORDER BY Timesent DESC LIMIT $_GET[start],30", 
								$_SESSION['Board'], addslashes($user['Name']));
				break;
			case "2.0":
				$sql = sprintf("SELECT * FROM Messages WHERE (boardid = %d) order by timesent desc LIMIT $_GET[start],30", $_SESSION['Board']);
				break;
		}
		//echo "sql=$sql<BR>";
		$msgs = get_query_array($sql);
		if($msgs) {
			echo '<table>';
			echo '<tr><th style="text-align: left">Sender</th><th style="text-align: left">Time</th><th style="text-align: left">Subject</th></tr>';
			foreach($msgs as $msg) {
				$link = '<a href="'.$PHP_SELF.'?msgid='.$msg['msgid'].'">';
				echo '<tr>';
				//echo '<td>'.$link.$msg['msgid'].'</a></td>';
				echo '<td>';
				if($msg['msgid'] > $lastread)
					echo '<img src="images/new.png">';
				echo $msg['sender'];
				echo '</td>';
				echo '<td>';
				echo mydate($msg['timesent']);
				echo '</td>';
				//echo '<td>'.$link.$msg['timesent'].'</a></td>';
				echo '<td>'.$link.$msg['subject'].'</a></td>';
				if($msg['deleted'])
					echo '<td>**CANCELLED**</td>';
				echo '</tr>';
			}
			echo '</table>';
		} else {
			echo "No messages.<BR>";
		}
		echo '<br>';
		if($_GET['start'] != 0) {
			$start = $_GET['start'] - 30;
			if($start < 0) $start = 0;
			echo '<a href="'.$_SERVER['PHPSELF'].'?start='.$start.'" style="float:left">Prev</a>';
		}
		if($msgs) {
			$start = $_GET['start'] + 30;
			echo ' <a href="'.$_SERVER['PHPSELF'].'?start='.$start.'" style="float:right">Next</a>';
			echo '<br>';
		}
	} else {
		$_SESSION['movethrottle']--;
		$sql = "SELECT * FROM Messages WHERE (msgid={$_GET[msgid]} AND boardid=".$_SESSION['Board'].")";
		$row = get_query($sql);
		$sql = "SELECT msgid FROM Messages WHERE (msgid > ".$_GET['msgid']." AND boardid=".$_SESSION['Board']." and deleted=0) ORDER BY msgid LIMIT 1";
		$nextmsg = get_query($sql);
		$nextmsg = $nextmsg['msgid'];
		$sql = "SELECT msgid FROM Messages WHERE (msgid < ".$_GET['msgid']." AND boardid=".$_SESSION['Board']." and deleted=0) ORDER BY msgid DESC LIMIT 1";
		$prevmsg = get_query($sql);
		$prevmsg = $prevmsg['msgid'];
		
		$sql = sprintf("SELECT count(*) AS num FROM Messages as num WHERE (parentid=%d)", $_GET['msgid']);
		$numreplies = get_query($sql);
		//print_r($numreplies);
		$numreplies = $numreplies['num'];
		
		if($prevmsg) {
			$link = '<a href="'.$_SERVER['PHPSELF'].'?msgid='.$prevmsg.'" style="float:left">';
			echo "{$link}Previous</a> ";
		}
		if($nextmsg) {
			$link = '<a href="'.$_SERVER['PHPSELF'].'?msgid='.$nextmsg.'" style="float:right">';
			echo "{$link}Next</a>";
		}
		echo '<br>';
		$lastread = get_last_read($user['userid'], $_SESSION['Board']);
		$usender = get_user($row['sender']);
		if($row['msgid'] > $lastread) {
			set_last_read($user['userid'], $_SESSION['Board'], $row['msgid']);
			if($usender && $user['userid'] != $usender['userid']) {
				mysql_query("UPDATE users SET Fame=Fame+0.10 WHERE userid=".$usender['userid']);
			}
		}
		if($usender) {
			if($usender['GroupID'] > 0)
				$ugroup = load_group($usender['GroupID']);
			if($ugroup)
				$groupname = "(".$ugroup['Name'].")"; // for like medieval settings, add "of" before hand :D
			$fromname = sprintf("%s (%s) %s", $usender['Name'],get_level_title($usender['Level']), $groupname);
		} else {
			$fromname = $row['sender'];
		}
		if($row['parentid']) {
			$sql = "SELECT sender,msgtext,deleted FROM Messages WHERE (msgid={$row[parentid]} AND boardid=".$_SESSION['Board'].")";
			$msg = get_query($sql);
		}

		$link = '<a href="'.$_SERVER['PHPSELF'].'?msgid='.$row['parentid'].'">';
			echo "
				<table class=msgheader>
					<tr>
						<th class=msgheader style=border:none>Message #{$row[msgid]}</th><td>Posted On {$board[boardname]}";
			if($row['parentid']) {
				if($msg['deleted'])
					printf(" (reply to cancelled message)");
				else
					echo " (reply to #$link{$row[parentid]}</a>)";
			}
			if($numreplies)
				printf(" - %d repl%s", $numreplies, ($numreplies > 1 ? "ies" : "y"));
			
			if(date("m-d") == "09-19") {
				// TALK LIKE A PIRATE!
				$url = "http://www.syddware.com/cgi-bin/pirate.pl";
				$params = "text=".bb2html($row['msgtext']);
				$user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_POST,1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
				curl_setopt($ch, CURLOPT_URL,$url);
				//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
				curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  // this line makes it work under https

				$pir=curl_exec ($ch);
				curl_close ($ch);
				$pir = explode('this way: </font>', $pir);
				$pir = $pir[1];
				$pir = explode('<font color="red" size="-1">', $pir);
				$pir = $pir[0];
				$row['msgtext'] = $pir;
			}
			$TITLE = $row['subject'];
			echo get_message_menu($row[msgid])."</td>
					</tr><tr>
						<th class=msgheader style=border:none>From:</th><td><nobr><address style=display:inline>{$fromname}</address></nobr>".get_user_menu($row['sender'])."</td>
					</tr><tr>
						<th class=msgheader style=border:none>Time:</th><td>".mydate($row[timesent])."</td>
					</tr><tr>
						<th class=msgheader style=border:none>Subj:</th><td>{$row[subject]}</td>
					</tr>
				</table>
				<hr><div style=border:none>
				".bb2html($row[msgtext])."</div>";
			echo '<p><div style="text-align: center; border: none;">';
			if($prevmsg) {
				$link = '<a href="'.$_SERVER['PHPSELF'].'?msgid='.$prevmsg.'" style="float:left; vertical-align: middle" title="Previous Message">';
				echo "{$link}Previous</a> ";
			}
			echo '<a href="read.php" style="verical-align: middle" title="Return to Message List">Return to List</a>';

			if($nextmsg) {
				$link = '<a href="'.$_SERVER['PHPSELF'].'?msgid='.$nextmsg.'" style="float:right; vertical-align: middle" title="Next Message">';
				echo "{$link}Next</a>";
			}
			echo '</div>';
			echo '<form name="reply" method="post" action="post.php"><p>
				<input type="hidden" name="parentid" value="'.$row['msgid'].'">
				<input type="hidden" name="subject" value="'.$row['subject'].'">
				<input class="submit" type="submit" value="Reply">
				</form>';
			if($row['parentid']) {
				echo '<p><div class="windowtitle">Original Message from '.$msg['sender'].'</div>';
				echo '<div class="window">';
				if($msg['deleted'])
					echo "Original message cancelled.<BR>";
				else
					echo bb2html($msg['msgtext']);
				echo '</div>';
				echo '</div>';
			}
	}
	
	require_once('end.php');
?>
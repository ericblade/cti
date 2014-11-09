<?PHP
	require_once('cti.php');
	
	if(!$user['LocX']) $user['LocX'] = "0";
	if(!$user['LocY']) $user['LocY'] = "0";

	$dist = get_distance_from_base($user['LocX'], $user['LocY']);
	$lev = get_level_number($dist);
	$tit = get_level_title($dist);
	//echo "dist=$dist lev=$lev title=$tit<BR>";
	switch($_GET['cmd']) {
		case 'change':
			//$_SESSION['Board'] = $_GET['id'];
			if($_GET['id'] == $lev) {
				$board['boardid'] = $lev;
				$board['level'] = $dist;
				$board['boardname'] = $tit;
				$board['description'] = "The board for ".$tit."s";
			} else {
			//echo "Finding board $_GET[id]<BR>";
				if($user['Level'] > 1000 || $user['userid'] == 1)
					$sql = "SELECT * FROM Boards where BoardID=$_GET[id]";
				else
					$sql = "SELECT * FROM Boards where ( ((LocX = 0 AND LocY = 0) or (LocX=$user[LocX] AND LocY=$user[LocY])) and level <= $user[Level] AND boardid=$_GET[id] AND Destroyed=0)";
			//echo $sql."<BR>";
				$board = get_query($sql);
			}
			//print_r($board);
			$_SESSION['Board'] = $board['boardid'];
			
			/*echo "Now accessing board #$board[boardid], '$board[boardname]'.<BR>";
			echo "<P>Use the Read and Post commands to operate this board.<BR>";*/
			$_SESSSION['Command'] = "";
			if(!$board) {
				echo "Board change failed.<BR>";
			} else {
				header("Location: read.php");
			}
			break;
		default:
			switch($_GET['sort']) {
				case "level": $orderby = "ORDER BY Level"; break;
				default: $orderby = "ORDER BY boardname"; break;
			}
			$sql = sprintf("SELECT * FROM Boards WHERE (boardid < 0 AND Level <= %d AND Destroyed = 0) %s", $user['Level'], $orderby); // Get system boards first
			$bs = get_query_array($sql);
			foreach($bs as $b) {
				$boards[sizeof($boards)] = $b;
			}

			$sql = sprintf("SELECT * FROM Boards WHERE (boardid < 11 AND boardid >= 0 AND Level <= %d AND Destroyed = 0) %s", $user['Level'], $orderby); // Then band boards
			if($user['Level'] > 1000 || $user['userid'] == 1)
				$sql = "SELECT * FROM Boards";

			$bs = get_query_array($sql);
			foreach($bs as $b) {
				$boards[sizeof($boards)] = $b;
			}

			// everything else
			$sql = sprintf("SELECT * FROM Boards WHERE ( ( (LocX=0 AND LocY=0) or (LocX=%d AND LocY=%d) ) AND Level <= %d AND boardid > 11 AND Destroyed = 0) %s",
							$user['LocX'], $user['LocY'], $user['Level'], $orderby);
			//$sql = "SELECT * FROM Boards where ( ((LocX = 0 AND LocY = 0) or (LocX=$user[LocX] AND LocY=$user[LocY]))  and level <= $user[Level]) ORDER BY boardname";
			$bs = get_query_array($sql);
			foreach($bs as $b) {
				$boards[sizeof($boards)] = $b;
			}
			//$boards['current']['boardid'] = $lev;
			//$boards['current']['level'] = $dist;
			//$boards['current']['boardname'] = $tit;
			//$boards['current']['description'] = "The board for ".$tit."s";
			if($boards) {
				echo '<table>';
				echo '<tr><th style="text-align: left;"><a href="'.$_SERVER['PHP_SELF'].'?sort=name" title="Sort by Name">Name</a></th><th style="text-align: left;">Description</th><th>Messages</th><th><a href="'.$_SERVER['PHP_SELF'].'?sort=level" title="Sort by Level">Access</a></th></tr>';
				foreach($boards as $board) {
					if($listed[$board['boardid']]) continue;
					if($board['Destroyed']) continue; // TODO: check if we get Destroyed boards in list, this should get it out for Super user.
					$listed[$board['boardid']] = 1;
					if($board['creator']) {
						$u = get_user($board['creator']);
						//print_r($board['creator']);
						$creator = $u['Name'];
					} else {
						$creator = "CTNet";
					}
					$link = '<a href="'.$_SERVER['PHPSELF'].'?cmd=change&amp;id='.$board['boardid'].'" TITLE="Created By '.$creator.'">';
					$sql = "select count(msgid) as c from messages where boardid=".$board['boardid'];
					$c = get_query($sql);
					$c = $c['c'];
					
					$lastread = get_last_read($user['userid'], $board['boardid']);
					$highmsg = get_high_message($board['boardid']);
					
					echo '<tr>';
					echo '<td>';
					if($highmsg > $lastread) {
						echo '<img src="images/new.png" alt="New!">';
					}
					echo $link.$board['boardname'];
					echo '</a></td>';
					echo '<td>'.$board['description'].'</td>';
					echo '<td style="text-align: center;">'.$c.'</td>';
					echo '<td style="text-align: center;">'.$board['level'].'</td>';
					echo '</tr>';
				}
				echo '</table>';
			} else {
				echo "No boards found.<BR>";
			}
			break;
	}
	
	require_once('end.php');
?>
					
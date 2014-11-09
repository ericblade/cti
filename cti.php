<?php
	require_once('include/config.php');
	require_once('include/BBCodeToHTML.php');
	require_once('include/database.php');
	require_once('include/user.php');
	require_once('include/strings.php');
	
	// Begin session if we don't have one currently, and assign a sane default username if it's a new session
	if (!isset ($_SESSION)) 
		session_start();
	if(!$_SESSION['username']) {
		$user['Name'] = "Unknown";
	}

	function get_distance_from_base($x, $y) {
		return round(hypot($x, $y));
	}
	
	function get_distance_from_point($x1, $y1, $x2, $y2) {
		return round(hypot($x1 - $x2, $y1 - $y2));
	}
	
	function get_level_number($level) {
		$num = 0;
		if($level > 0) $num = 1;
		if($level > 3) $num = 2;
		if($level > 7) $num = 3;
		if($level > 15) $num = 4;
		if($level > 31) $num = 5;
		if($level > 63) $num = 6;
		if($level > 127) $num = 7;
		if($level > 256) $num = 8;
		if($level > 500) $num = 9;
		if($level > 1000) $num = 10;
		return $num;
	}
		
	function display_location_info() {
		global $user, $UNREADHERE, $LOCBOARDSHERE, $PLAYERSHERE;
		$newlevel = get_level_number(get_distance_from_base($user['LocX'], $user['LocY']));
		if(get_high_message($newlevel) > get_last_read($user['userid'], $newlevel)) {
			echo $UNREADHERE;
		}
		if($user['LocX'] != 0 || $user['LocY'] != 0) {
			if(get_boards_at_loc($user['LocX'], $user['LocY'])) {
				echo $LOCBOARDSHERE;
			}
		}
		$users = get_users_at_loc($user['LocX'], $user['LocY']);
		if($users) {
			echo $PLAYERSHERE;
			echo '<div class="window">';
			foreach($users as $u) {
				echo $u['Name']."<BR>";
			}
			echo '</div>';
		}
	}
	
	function display_main() {
		global $user, $BN_MOTD, $MOTDHEADER, $ACTIVITYHEADER;
		$motds = get_messages($BN_MOTD, time() - (1440 * 60), 5);
		if($motds) {
			printf($MOTDHEADER, (sizeof($motds) > 1 ? "s" : ""));
			echo '<div class="window">';
			//echo '<img class="titlebutton" alt="X">';
			echo '<table>';
			foreach($motds as $m) {
				printf('<tr><td>%s</td><td><a href="read.php?msgid=%d&amp;motd=1">%s</a></td></tr>', mydate($m['timesent']), $m['msgid'], censor_text($m['subject']));
			}
			echo '</table>';
			echo '</div>';
		}
		$acts = get_events(0, 5);
		if($acts) {
			echo $ACTIVITYHEADER;
			echo '<div class="window">';
			echo '<table>';
			foreach($acts as $a) {
				printf("<tr><td>%s</td><td>%s</td></tr>", mydate($a['Timesent']), censor_text(stripslashes($a['Event'])));
			}
			echo '</table>';
			echo '</div>';
		}
// TODO: Maybe this can be done somehow using background images..
// TODO: Test in IE7 and Firefox
		echo '<img src="images/ctilogo1.jpg" style="position:absolute; /*top: 0;*/ left: 0; bottom: 0; /*right: 0;*/ filter:alpha(opacity=25); opacity: 0.25; -moz-opacity:0.25; z-index: -1;">';
	}
		
	function get_exhaust() {
		global $user;
		if($user['userid'] == 0) return;
		$sql = sprintf("SELECT * FROM `exhaust` WHERE userid=%d", $user['userid']);
		$exh = get_query($sql);
		//print_r($exh);
		//printf("get_exhaust(): exhaust=%s<BR>", $exh['exhaust']);
		return $exh['exhaust'];
	}
	
	function save_exhaust() {
		global $user, $_SESSION;
		if($user['userid'] == 0) return;
		if(!$_SESSION['movethrottle']) return;
		//printf("save_exhaust(): exhaust=%s<BR>", $_SESSION['movethrottle']);

		$sql = sprintf("UPDATE users SET lastcmdtime=%d WHERE userid=%d", time(), $user['userid']);
		mysql_query($sql);
		$x = get_exhaust();
		if(isset($x)) {
			$sql = sprintf("UPDATE exhaust SET exhaust=%d WHERE userid=%d", $_SESSION['movethrottle'], $user['userid']);
			$res = mysql_query($sql);
			//echo "res=$res<BR>";
			//echo mysql_error()."<BR>";
		} else {
			$sql = sprintf("INSERT INTO exhaust (userid,exhaust) VALUES (%d,%d)", $user['userid'], $_SESSION['movethrottle']);
			$res = mysql_query($sql);
			//echo "res=$res<BR>";
			//echo mysql_error()."<BR>";
		}
	}
	
	function same_sector($x1, $y1, $x2, $y2) {
		if(get_sector($x1, $y1) == get_sector($x2, $y2)) return 1;
		return 0;
	}
	
	function valid_target($u1, $u2) {
		if(!same_sector($u1['LocX'], $u1['LocY'], $u2['LocX'], $u2['LocY']))
			return "$u2[Name] does not seem to be in this sector.<BR>";
		if($u1['Level'] <= $u2['Level'])
			return "$u2[Name] has too high of an access level for this to work.<BR>";
		return 1;
	}
	
	function get_sector($x, $y) {
		//echo "get_sector($x, $y)<BR>";
		if($x == 0 && $y == 0) return 0;
		if($x >= 0) {
			if($y >= 0) return 1;
			else return 4;
		} else {
			if($y >= 0) return 2;
			else return 3;
		}
	}
	
	function allocate_credits() {
		global $user;

		if(!$user['lastcmdtime'] && $user['userid'] > 0) // TODO: remove this after everyone who was a current user when it was added has logged in to update properly
			$user['lastcmdtime'] = time() - ( (60 * 60) * 24);
		
		if($user['lastcmdtime'] && $user['Level'] < 256 && $user['userid'] > 0) {
			if($user['Level'] < 25) $maxrest = 50 + $user['Level'];
			else $maxrest = 25 + ($user['Level'] * 2);
			//$maxrest = (25 + $user['Level']);
			//$user['lastcmdtime'] -= ((60 * 60) * 5);
			$hours = (((time() - $user['lastcmdtime']) / 60) / 60);
			//$hours = round(round((time() - $user['lastcmdtime']) + 6000 / 60) / 60);
			if($hours >= 1) {
				$_SESSION['movethrottle'] -= $hours * 5;
				//echo "Daily Allocation: $maxrest<BR>";
				//echo "Hours since last command: $hours<BR>";
				//printf("Allocation per hour: %f<BR>", (float)($maxrest / 24));
				
				$add = round( ($maxrest / 24) * $hours);
				if($add > 0) {
					$pool = QueryStaminaPool();
					if($add > $pool) {
						$add = $pool;
					}
					if($add > 0) {
						printf($ALLOCATEDSTAMINA, $add);
						AdjustStaminaPool(-$add);
						$user['Stamina'] += $add;
					}
				}
			}
		}
	}

	ob_start();

	// If the session the web browser sent us claimed to be logged in and have a username, 
	// then let's try and re-load that user in case anything good or bad happened to us since the last
	// time we loaded a page
	if($user['Name'] != "Unknown") {
		$user = get_user($_SESSION['username']);
		//echo "user=$user<BR>";
	}
	
	// Get user's Group Name
	if($user['GroupID'] > 0) {
		$user['Group'] = load_group($user['GroupID']);
	}
	if(!$user['Group'] || $user['Group'] == "") 
		$user['Group']['Name'] = "None";
	//else 
//		$user['Group'] = $user['Group']['Name'];
		
	//print_r("group=$user[Group]<BR>");
	// TODO: Since the save functions should now be able to deal with numeric vs. string values, we may want to remove this level conversion 
	if(!$user['Level']) 
		$user['Level'] = "0";
	// give guests a tiny bit of power
	if(!isset($user['Stamina']))
		$user['Stamina'] = "10";

	// Get the command name as entered in the "Command" db table, as well as it's cost and version
	$filename = basename($_SERVER['SCRIPT_NAME']);
	//echo "filename=$filename";
	if($_GET['cmd']) {
		$sql = sprintf("SELECT DISTINCT Command, max(Version) AS V, max(Cost) AS Cost, Program FROM commands WHERE (Level <= %d AND Program='%s' AND Command='%s') GROUP BY Command",
				$user['Level'], $filename, $_GET['cmd']);
	} else {
		$sql = sprintf("SELECT DISTINCT Command, max(Version) AS V, max(Cost) AS Cost, Program FROM commands WHERE (Level <= %d AND Program='%s') GROUP BY Command",
				$user['Level'], $filename);
	}
	$command = get_query($sql);
	//print_r($command);
	$warezname = $command['Command'];
	$version = $command['V'];
	//$command['Cost'] = $command['C'];
	//$version = $command['max(Version)'];
	//$command['Cost'] = $command['max(Cost)'];
	//print_r($command);
	if($command && $command['Cost'] > 0 && !$_GET['deletetell']) { // TODO: this last part could cause us some headaches?
		// TODO: perhaps play a CHEATER! message if deletetell fails? 
		if($_SESSION['command'] != "$warezname $version") {
			//echo "prev command=".$_SESSION['command']."<BR>";
			$_SESSION['command'] = "$warezname $version";
			$user['Stamina'] -= $command['Cost'];
			echo "Cost: $command[Cost]<BR>";
		} // It will be the command's responsibility to clear the command when it is done executing, so pages that have confirmation and such won't double drain credits
	}
	
	require_once('maintenance.php');

	// If a player goes over their stamina limit, return it to the Stamina Pool
	$maxstam = 100 + ($user['Level'] * 50); // TODO: Make it (level + 1) * 50 .. make sure change goes to finger too
	if($user['Stamina'] > $maxstam) {
		$newstam = $user['Stamina'] - $maxstam;
		$user['Stamina'] = $maxstam;
		printf($OVERLOADEDSTAMINA, $newstam);
		
		AdjustStaminaPool($newstam);
	}
	
	// If time limits are configured on, then set the end time, otherwise it doesn't need to be set.. 
	// There's no reason -not- to set it here, just that the original unimplementation of time limits just commented this section out.. 
	// TODO: As said in the config.php, someone should bother testing time limits, if they'd like that feature.
	if($USETIMELIMITS && $_SESSION['LoginTime']) {
		$minutes = 15 + ($user['Level'] * 3);
		//$minutes = (12 * ($user['Level']+1));
		if($minutes > 120) $minutes = 120;
		$endtime = $_SESSION['LoginTime'] + ($minutes * 60);
	}
	
	if($user['userid']) {
		// If we have a valid user, let's check their messages and stuff

		// if we've been sent a delete tell command, let's do that before dealing with anything else.
		// TODO: In the future, this may have to short circuit any other instructions coming in, as it could allow free commands to get through, if the cost system is changed from the way it 
		// was originally implemented.
		if($_GET['deletetell']) {
			delete_tell($user['userid'], $_GET['deletetell']);
		}
		$sql = sprintf("SELECT * FROM tells WHERE (toid=%d)", $user['userid']);

		$tells = get_query_array($sql); // use userid so it gets there even if names change
		if($tells) {
			echo $TELLHEADER;
			echo '<div class="window">';
			foreach($tells as $tell) {
				echo '<b>';
				//echo date("m-d-y h:m:t", $tell['timesent']);
				echo mydate(date("Y-m-d H:m:s", $tell['timesent']));
				echo ' ';
				if(!$tell['autodelete']) {
					printf(' <a href="%s?deletetell=%d" title="Delete Tell">Delete</a> ', $_SERVER['PHP_SELF'], $tell['id']);
				}
				echo $tell['message'];
				echo '</b>';
				if($tell['fromname'])
					echo '<br>'.get_user_menu($tell['fromname']).'<br>';
				echo '<br>';
			}
			echo '</div>';
			echo '</div>';
		}
		$sql = "DELETE FROM tells WHERE (toid=".$user['userid']." AND autodelete=1)";
		mysql_query($sql);
		$exhaust = get_exhaust();
		if($exhaust)
			$_SESSION['movethrottle'] = $exhaust;
		if($_SESSION['movethrottle'] > 0) {
			$_SESSION['movethrottle']--;
		} else if ($_SESSION['movethrottle'] < 0) {
			$_SESSION['movethrottle'] = 0;
		}
	} else {
		$user['Group'] = "None";
	}
	//echo "exhaust=$_SESSION[movethrottle]<BR>";
	
	allocate_credits();
	
	$dist = get_distance_from_base($user['LocX'], $user['LocY']);
	if($dist > $user['Level']) {
		echo $NOACCESSTOLOC;
		$user['LocX'] = $user['HomeX'];
		$user['LocY'] = $user['HomeY'];
		$a = abs($user['LocX']);
		$b = abs($user['LocY']);
		$dist = round(sqrt(pow($a, 2) + pow($b, 2)));
		if($dist > $user['Level']) {
			echo $NOACCESSTOHOME;
			$user['LocX'] = 0;
			$user['LocY'] = 0;
		}
	}
	error_log(date("Y-m-d H:i:s").": $user[Name]: ".$_SERVER['PHP_SELF']."<BR>\r\nPOST=".print_r($_POST, 1)."<BR>\r\nuser=".print_r($user, 1), 3, "activity.log");
	
	if( (!loggedin() && rand(0,4) == 0) || rand(0,20) == 0) {
		// TODO: Move this into the templates thing
		echo get_random_quote()."<P>";
	}
	//print_r($_SESSION);
	//echo "<BR>";
?>	
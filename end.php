<?php

	if($user['Banned']) {
		logout("It looks like you've made an enemy or two.  You've been BANNED.");
	}
	if($user['Stamina'] < 0) {
		logout("You seem to be out of credit.  Please come back when you have more. (Credits will allocate on an hourly basis, so you will want to wait at least an hour)");
	}
	//echo "time:".time()." endtime: $endtime<BR>";
	// TODO: Use Time Limit config var?
	if($USETIMELIMITS && $_SESSION['LoginTime'] && $endtime) {
		if(time() > $endtime) {
			logout($TIMEEXPIRED);
		}
	}
	/*if($_SESSION['LoginTime'] && $endtime) {
		if(time() > $endtime) {
			logout("Your time for this session has expired.  Higher access levels may stay longer.");
		}
	}*/
	if(!$warezname) $warezname = $_SERVER['PHP_SELF'];
	if(!$version) $version = "0.1";
	
	if(!$user || !isset($user)) {
		$user['Name'] = "Unknown";
		$user['Level'] = "0";
		$user['LocX'] = "0";
		$user['LocY'] = "0";
		$user['Stamina'] = "0";
		$user['Fame'] = "0";
	}
	if(!$user['Level']) {
		$user['Level'] = "0";
		if(!$user['LocX']) $user['LocX'] = "0";
		if(!$user['LocY']) $user['LocY'] = "0";
		if(!$user['Stamina']) $user['Stamina'] = "0";
		if(!$user['Fame']) $user['Fame'] = "0";
	}
	$user['Distance'] = get_distance_from_base($user['LocX'], $user['LocY']);

	if($user && $user['Name'] != "Unknown") {
		save_user($user);
		save_exhaust();
	}

	$temp = file_get_contents('cti.html');
	$temp = str_replace('%MAINAREA%', ob_get_contents(), $temp);
	// TODO: put mainarea below the others so people can't use them?  or select certain things that people could use .. hmm..
	$temp = str_replace('%USERNAME%', $user['Name'], $temp);
	$temp = str_replace('%USERLEVEL%', $user['Level'], $temp);
	$temp = str_replace('%USERTITLE%', get_level_title($user['Level']), $temp);
	$temp = str_replace('%DISTTITLE%', get_level_title($user['Distance']), $temp); // TODO get_level_title function :)
	$temp = str_replace('%USERLOCX%', $user['LocX'], $temp);
	$temp = str_replace('%USERLOCY%', $user['LocY'], $temp);
	$temp = str_replace('%USERDISTANCE%', $user['Distance'], $temp);
	$temp = str_replace('%USERSTAMINA%', $user['Stamina'], $temp);
	$temp = str_replace('%USERFAME%', get_fame_title($user['Fame'], $user['Level']), $temp); // TODO: get_fame_title function
	$temp = str_replace('%WAREZNAME%', $warezname, $temp);
	$temp = str_replace('%WAREZVERSION%', $version, $temp);
	$temp = str_replace('%CURRENTTIME%', mydate((int)time()), $temp);
	
	if(!is_array($user['Group'])) {
		$user['Group'] = array();
		$user['Group']['Name'] = "None";
	}
	$temp = str_replace('%USERGROUP%', print_r($user['Group']['Name'], 1), $temp);
	
	$temp = str_replace('%TITLE%', $TITLE, $temp);
	
	$sql = sprintf("SELECT number FROM invites WHERE id=%d", $user['userid']);
	$invitecount = get_query($sql);
	$invitecount = $invitecount['number'];
	if(!$invitecount) $invitecount = "0";
	$temp = str_replace('%INVITES%', $invitecount, $temp);
	
	if($_SESSION['LoginTime'] < 1 || $endtime < 1) $timeremaining = "Unknown";
	else {
		$timeremaining = round((($endtime - time()) / 60));
		$timeremaining .= " min";
		
		/* Our "kind of" UIT implementation overrides the usage of standard time */
		$timeremaining = floor( ($endtime - time()) / 86.4);
	}
	$temp = str_replace('%TIMEREMAINING%', $timeremaining, $temp);
	
	$commands = get_query_array("SELECT DISTINCT Command, Grouping, max( Version ) , Program, ShowOnMenu FROM commands WHERE (Level <= ".$user['Level']." and ShowOnMenu> 0) GROUP BY Command ORDER BY Grouping,Command");

	// TODO: command categories?
	//$menu = '<TABLE style="text-align: center; margin-left: auto; margin-right: auto;">';
	//$menu = '<div style="text-align: justify; padding: 2px; border: none;">';
	//$menu = '<div style="text-align: right; padding: 2px; border: none;">';
	$menu = '<div>';
	if(!loggedin()) {
		//$menu .= '<tr><td style="text-align: left;">';
		//$menu .= '<a  style="padding-right:5px; padding-left:5px;" href="login.php">Login</a><wbr> ';
		$menu .= '<a href="login.php">Login</a><wbr> ';
		//$menu .= '</td><td style="text-align: right;">';
		//$menu .= '<a  style="padding-right:5px; padding-left:5px;" href="newuser.php">NewUser</a><wbr> ';
		$menu .= '<a href="newuser.php">NewUser</a><wbr> ';
		//$menu .= '</td></tr>';
	}
	for($x = 1-loggedin(); $x < sizeof($commands); $x++) {
		$menu .= "\r\n";
		// TODO: add tool tip to command database and link?
		if($lastgrouping != $commands[$x]['Grouping']) {
			//if($x > 0) $menu .= '<br>';
			//$menu .= $commands[$x]['Grouping'].'<br>';
			$menu .= '</div><span>'.$commands[$x]['Grouping'].'</span><div>';
			$groupcount = 0;
		}
		$groupcount++;

		$link = '<a href="'.$commands[$x]['Program'].'?cmd='.$commands[$x]['Command'].'" title="'.$commands[$x]['Command'].' '.$commands[$x]['max( Version )'].'">';
			// style="padding-left: 5px; padding-right:5px;"
		//$menu .= '<TR><TD style="text-align: left;">';
		$menu .= $link.$commands[$x]['Command'].'_'.$commands[$x]['max( Version )'].'</a>';
		//$menu .= '&nbsp;<wbr> ';
		$menu .= '<wbr>';
		$lastgrouping = $commands[$x]['Grouping'];
		//$x++;
		//if($commands[$x]) {
		//	$link = '<a href="'.$commands[$x]['Program'].'">';
		//	$menu .= '<TD style="text-align: right;">'.$link.$commands[$x]['Command'].'&nbsp;'.$commands[$x]['max( Version )'].'</a></td>';
		//}
		//$menu .= '</td></tr>';
	}
	if(loggedin() && ($user['GroupID'] > 0 || $user['Level'] > 63)) {
		$menu .= '<br><a href="group.php">UserGroups</a><wbr>';
	}
	if(loggedin()) {
		//$menu .= '<tr><td style="text-align: left;">';
		$menu .= '<br><a href="logout.php">Logout</a><wbr>';
		//$menu .= '</td></tr>';
	}
	//$menu .= '</table>';
	$menu .= '</div>';
	$temp = str_replace('%MENUTABLE%', $menu, $temp);
	
	$temp = str_replace('%SPECIALMENU%', get_special_menu($user['LocX'], $user['LocY']), $temp);
	
	ob_end_clean();
	echo $temp;
	exit();
?>
<?PHP
	// string manipulation and return functions

	function mydate($date) {
		//2006-09-03 03:38:35
		if(is_int($date)) {
			$utime = $date;
		} else {
			
			list($year, $month, $day, $hour, $minute, $second) = sscanf($date, "%d-%d-%d %d:%d:%d");
			$utime = mktime($hour, $minute, $second, $month, $day, $year);
		}
		$starttime = mktime(9, 2, 43, 8, 10, 2006); // TODO: Derive this from system config info?
		$mytime = $utime - $starttime;
		$days = floor($mytime / 86400);
		$secs = $mytime - ($days * 86400); // remainder of time left
		$beats = floor($secs / 86.4);
		return sprintf("%03d&nbsp;@%03d", $days, $beats); 
	}
	
	function get_input_tag($name, $password=0) {
		return sprintf('<input type="%s" class="text" name="%s" onfocus="this.style.border=\'2px solid black\';" onblur="this.style.border=\'2px solid transparent\';">', $password ? "password" : "text", $name);
	}
	
	function get_message_menu($num) {
		global $user;
		$sql = "SELECT DISTINCT Command, max( Version ) , Program, ShowOnMenu FROM commands WHERE (Level <= ".$user['Level']." and ShowOnMessages > 0) GROUP BY command";
		//echo $sql."<BR>";
		$commands = get_query_array($sql);
		if(!$commands) return "";
		//$menu = '<table><tr>';
		foreach($commands as $cmd) {
			$menu .= sprintf('<span style="float:none; padding-left: 2%%;"><a href="%s?msgnum=%d" title="%s Message">%s</a></span>', $cmd['Program'], $num, $cmd['Command'], $cmd['Command']);
			//$menu .= '<td><a href="'.$cmd['Program'].'?msgnum='.$num.'">'.$cmd['Command'];
			//$menu .= '</a></td>';
		}
		//$menu .= '</tr></table>';
		return $menu;
	}
	
	function get_user_menu($name) {
		global $user;
		$sql = "SELECT DISTINCT Command, max( Version ), Program, ShowOnUsers FROM commands WHERE (Level <= ".$user['Level']." and ShowOnUsers > 0) GROUP BY Command";
		$commands = get_query_array($sql);
		if(!$commands) return "";
		//$menu = '<table><tr>';
		//echo '<td>';
		foreach($commands as $cmd) {
			$menu .= sprintf('<span style="float: none; padding-left: 2%%;"><a href="%s?name=%s" title="%s %s">%s</a></span>', $cmd['Program'], urlencode($name), $cmd['Command'], $name, $cmd['Command']);
			//$menu .= '<td><a href="'.$cmd['Program'].'?name='.urlencode($name).'">'.$cmd['Command'];
			//$menu .= '</a></td>';
		}
		//echo '</td>';
		//$menu .= '</tr></table>';
		return $menu;
	}
	
	function get_special_menu($LocX, $LocY) {
		$sql = "SELECT Program,Command FROM Specials WHERE (LocX=$LocX AND LocY=$LocY)";
		$commands = get_query_array($sql);
		if(!$commands) return "";
		$menu = '<table>';
		foreach($commands as $cmd) {
			$menu .= '<tr><td><a href="'.$cmd['Program'].'" title="'.$cmd['Command'].'">'.$cmd['Command'].'</a></td></tr>';
		}
		$menu .= '</table>';
		return $menu;
	}
	
	function get_level_title($level) {
		// TODO: put these into a database
		// TODO: add a function to make a select box for these, we can have user-created boards that are limited to a band, rather than a specific access level.
		$title = "utter n00b";
		if($level > 0) $title = "n00b";
		if($level > 3) $title = "Advanced n00b";
		if($level > 7) $title = "Wilson";
		if($level > 15) $title = "Apprentice Artist";
		if($level > 31) $title = "Artist";
		if($level > 63) $title = "Advanced Artist";
		if($level > 127) $title = "Experienced Artist";
		if($level > 256) $title = "Cowboy";
		if($level > 500) $title = "Ace";
		if($level > 1000) $title = "Ace of Spades";
		return $title;
	}
	
	function get_fame_title($fame, $level) {
		$a = $fame / 2;
		$title = "Zilch";
		if($a > 1) $title = "Very Low";
		if($a > 2) $title = "Low";
		if($level > 0) {
			if($a >= $level) $title = "Average";
			if($a >= $level + 10) $title = "&gt;Avg";
			if($a >= $level + 25) $title = "OMG";
			if($a >= $level + 50) $title = "OMGWTF";
			if($a >= $level + 100) $title = "OMGWTFBBQ";
		}
		return $title;
	}
	
	function user_select_form($showamount=0, $showcomment=0) {
		global $warezname, $_POST, $_GET, $version, $INPUTJS;
		if($_GET['name']) $name = $_GET['name'];
		else $name = $_POST['name'];
		if($_GET['amount']) $amount = $_GET['amount'];
		else $amount= $_POST['amount'];
		if(!$amount) $amount = 1;
		printf('<fieldset><legend>%s %s</legend><form name="userselect" method="post" action="%s">', 
			$warezname, $version, $_SERVER['PHP_SELF']);
		printf('<p><label for="name">User:</label><input class="text" type="text" maxlength="64" name="name" id="name" value="%s" title="Enter User Name" %s><br />',
			$name, $INPUTJS);
		if($showamount) {
			echo '<P><label for="amount">Repeat X:</label><input type="text" class="numeric" maxlength="20" name="amount" id="amount" value="'.$amount.'" title="Enter number of times to repeat" '.$INPUTJS.'><br>';
			$extrajs = "+document.forms.userselect.amount.value";
		}
		if($showcomment) {
			echo '<P><label for="comment">Comment:</label><input type="text" class="text" maxlength="64" name="comment" id="comment" title="Enter Comment(optional)" '.$INPUTJS.'><br />';
		}
		printf('<P><input class="submit" type="submit" value="%s" onmouseover="this.title=\'%s \'+document.forms.userselect.name.value+\' \'%s" %s>', $warezname, $warezname, $extrajs, $INPUTJS);
		echo '</form></fieldset>';
	}
	
	function fix_text($str) { 
		//$str = str_replace("\n\n", "<P>", $str);
		$str = str_ireplace('<', '&lt;', $str);
		$str = str_ireplace('>', '&gt;', $str);
		//$str = str_replace("\n", "<BR>\n", $str);
		$str = nl2br($str);
		$str = preg_replace('/\s\s+/', ' ', $str);
		return $str;
	}
	
	function censor_text($str) {
		$str = str_ireplace('arse', 'buttocks', $str);
		$str = str_ireplace('ass', 'buttocks', $str);
		$str = str_ireplace('bastard', 'unpleasant person', $str);
		$str = str_ireplace('git', 'unpleasant person', $str);
		$str = str_ireplace('bloody', 'bleeping', $str);
		$str = str_ireplace('bitch', 'beach', $str);
		$str = str_ireplace('clitoris', 'female sexual organ', $str);
		$str = str_ireplace('clit', 'female sexual organ', $str);
		$str = str_ireplace('cock', 'male sexual organ', $str);
		$str = str_ireplace('damn', 'darn', $str);
		$str = str_ireplace('dick', 'male sexual organ', $str);
		$str = str_ireplace('fuck', 'frack', $str);
		$str = str_ireplace('hell', 'heck', $str);
		$str = str_ireplace('piss', 'pee', $str);
		$str = str_ireplace('prick', 'male sexual organ', $str);
		$str = str_ireplace('pussy', 'female sexual organ', $str);
		$str = str_ireplace('shit', 'poop', $str);
		$str = str_ireplace('slut', 'promiscuous person', $str);
		$str = str_ireplace(' tit ', ' boobie ', $str);
		$str = str_ireplace(' tits ', ' boobies ', $str);
		$str = str_ireplace('whore', 'prostitute', $str);
		$str = str_ireplace('cunt', 'female sexual organ', $str);
		return $str;
	}
	
	function get_random_quote() {
		$random = rand(0,20);
		switch($random) {
			case 0:
				return "You have to play the game, to find out why you're playing the game.";
			case 1:
				return "The primary goal, is to win the game.";
			case 2:
				return "No system can ever be <b>totally</b> secure.";
			case 3:
				return "Let's see what you're made of.";
			case 4:
				return "This isn't happening, it only thinks it's happening.";
			case 5:
				return "Speech is the representation of the mind, and writing is the representation of speech.";
			case 6:
				return "To be useful, a system has to do more than just correctly perform some task.";
			case 7:
				return "This is about making machines more fathomable and more under the control of human beings.";
			case 8:
				return "We are about new ways of connecting people to computers, people to knowledge, people to the physical world,  and people to people.";
			case 9:
				return "They say you can only get out of a computer that which you put in, but that is not necessarily true.";
			case 10:
				return 'Minds choose what to do next. -Stan Franklin, "Artificial Minds"';
			case 11:
				return 'Artificial intelligence is no match for natural stupidity. -Internet Proverb';
			case 12:
				return 'The ability to learn faster than your competition is one of the best assets to have.';
			case 13:
				return 'Intelligence is what you use when you don\'t know what to do. -Jean Piaget';
			default:
				return "";
		}
	}
?>
<?php
	require_once('cti.php');
	$x = get_query_array("SELECT * FROM Messages");
	foreach($x as $m) {
		//echo "<P>$m[msgtext]<BR>Score:".rate_text($m[msgtext])."<BR>";
		$u = get_user($m['sender']);
		$user['Level'] = $u['Level'];
		$fame[$m['sender']] += rate_text($m[msgtext]);
	}
	$user['Level'] = 1024;
	print_r($fame);


	function rate_text($text) {
		global $user;
		//echo "rt($text)<BR>";
		$base = 100;
		
		$text = preg_replace('/\s\s+/', ' ', $text);
		//echo "pr text=$text<BR>";
		for($x = 0; $x < strlen($text); $x++) {
			switch($text[$x]) {
				case '.':
				case '!':
				case '?':
					$sc++;
					if($punc) $repeatedpunc++;
					$punc = 1;
					break;
				case ' ': 
					$wc++;
				default:
					$punc = 0;
					break;
			}
		}
		//if($wc && $sc)
		$base = $wc + 100;
		if($base < 100) $base = 100;
		if(strtoupper($text) == $text) 
			$base -= 100;
		if(strtolower($text) == $text)
			$base -= 20;

		$base -= $repeatedpunc;
		$sc -= $repeatedpunc; // sentence count decrease .. doh
		if($sc > 10) $base += 10;
		if($wc > 50) $base += 10;
		if( $wc && $sc && (($wc / $sc) > 5)) $base += 10;
		if(!$sc) $base -= 10; // no sentences!
	
		if(strstr($text, "unilaterally")) {
			echo "It seems you've said a magic word.<BR>";
			$base += 20;
		}
		if($user['Level'] < 2) $base *= 0.5;
		echo "<BR>rp=$repeatedpunc wc=$wc sc=$sc base=$base";		//act=".$base / ($user['Level']+1)."\r\n";

		return $base /  ($user['Level']+1);
	}
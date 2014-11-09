<?PHP
	function save_user($x, $create=0) {
		if($x['Name'] == "Unknown") return;
		$x['Name'] = addslashes(stripslashes($x['Name']));
		if(!$x['Level']) $x['Level'] = "0";
		if(!$x['LocX']) $x['LocX'] = "0";
		if(!$x['LocY']) $x['LocY'] = "0";
		if(!$x['Stamina']) $x['Stamina'] = "0";
		if(!$x['Fame']) $x['Fame'] = "0";
		if(!$x['Banned']) $x['Banned'] = "0";

		$sql = "UPDATE Users SET Name='{$x[Name]}', Password='{$x[Password]}', Level={$x[Level]}, LocX={$x[LocX]}, LocY={$x[LocY]}, Stamina={$x[Stamina]}, Fame={$x[Fame]}, Banned={$x[Banned]} WHERE Name='{$x[Name]}'";
		$res = mysql_query($sql); // TODO: apprently this line rrors for creating a new user.. fix.
		$err = mysql_error();
		//echo $sql."<BR>";
		if($err || !$res) {
			echo "<BR>*** If this is the first time you have logged in, please ignore the previous error.<BR>";
			echo "*** Otherwise, please report it in Feedback.<BR>";
			echo "*** err=$err<BR>";
			echo "*** sql=$sql<BR>";
		}
		
		//echo "save user query=$sql<BR>update res=$res<BR>";
		if(!$res || $create) {
			$sql = "INSERT INTO Users (Name,Password,Level,LocX,LocY,Stamina,Fame,lastcmdtime) 
								VALUES ('{$x[Name]}','{$x[Password]}',{$x[Level]},{$x[LocX]},{$x[LocY]},{$x[Stamina]},{$x[Fame]},".time().")";
			$res = mysql_query($sql);
			//echo "save user insert res=$res";
			if(!$res) {
				echo "Error ".mysql_error()." saving user";
			}
		}
	}
	
	function get_user($name) {
		//if(!$name) return 0;
		if((int)$name)
			$sql = sprintf("SELECT * FROM Users WHERE userid=%d", $name);
		else {
			$name = stripslashes($name);
			$sql = sprintf("SELECT * FROM Users WHERE Name='%s'", addslashes($name));
		}
		//echo "get_user($name) sql=$sql<BR>";
		$res = get_query($sql);
		
		if(!$res['Name']) return 0;
		return $res;
	}
	
	function loggedin() {
		global $user;
		if(!$user || $user['Name'] == "Unknown") return 0;
		if($user['loggedout']) return 0;
		return 1;
	}
	
	function logout($reason) {
		global $user;
		$user['loggedout'] = 1;
		echo "<P>$reason<BR>";
		if($user['Name'] != "Unknown") log_event($user['Name'] . " logged out", $user['Level']);
		echo "You've been dumped.<BR>";
		save_user($user);
		unset($_SESSION['username']);
		session_destroy();
		unset($user);
	}

?>
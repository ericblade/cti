<?PHP
	// ANYTHING that accesses the database must be in either HERE, or in CONFIG.php (and only then if it accesses "GameSettings")
	// TODO: That's going to require a lot more cleaning up in the middle of it all
	$DATABASE_NAME 		= 'cti';
	$DATABASE_USERNAME 	= 'root';
	$DATABASE_HOST 		= 'localhost';
	
	$res = mysql_connect($DATABASE_HOST, $DATABASE_USERNAME);
	$res = mysql_select_db($DATABASE_NAME);
	
	function get_query_array($sql) {
		global $_SERVER;
		$res = mysql_query($sql);
		$err = mysql_error();
		if($err) {
			echo "Error ".$err." querying database, sql=$sql file=$_SERVER[PHP_SELF]";
			require_once('end.php');
		}
		$num = mysql_numrows($res);
		for($x = 0; $x < $num; $x++) {
			$ret[$x] = mysql_fetch_array($res);
		}
		return $ret;
	}
	
	function get_query($sql) {
		global $_SERVER;
		//echo "get_query($sql)<BR>";
		$res = mysql_query($sql);
		$err = mysql_error();
		if($err) {
			echo "Error ".$err." querying database, sql=$sql file=$_SERVER[PHP_SELF]";
			require_once('end.php');
			exit();
		}
		return mysql_fetch_array($res);
	}
	
	function log_event($x, $level=0) {
		if(!$level) $level = "0";
		$x = addslashes($x);
		$x = strip_tags($x);
		$sql = sprintf("INSERT INTO eventlog (Timesent, Event, Level) VALUES ('%s', '%s', %d)",
			date('Y-m-d H:i:s'), $x, $level);
		mysql_query($sql);
	}
	
	function send_tell($from, $toid, $message, $autodelete=1) {
		$sql="INSERT INTO Tells (timesent, fromname, toid, message, autodelete) VALUES (".time().",'".$from."', $toid, '".$message."', $autodelete)";
		//echo $sql."<BR>";
		$res = mysql_query($sql);
		//echo $res."<BR>".mysql_error();
	}
	
	function delete_tell($userid, $tellid) {
		$sql = sprintf("DELETE FROM Tells WHERE (id=%d AND toid=%d)", $tellid, $userid);
		return mysql_query($sql);
	}
	
	function broadcast($message) {
		$sql = sprintf("SELECT * FROM Users WHERE lastcmdtime > %d", time() - (3600)); // last hour
		$users = get_query_array($sql);
		if($users) {
			foreach($users as $u) {
				if($u['userid'] != $user['userid']) {
					send_tell("", $u['userid'], $message, 1);
					$count++;
				}
			}
		}
	}

	function cancel_message($msgid) {
		global $user;
		$res = mysql_query("UPDATE Messages SET Deleted=1 WHERE msgid=$msgid LIMIT 1");
		if(!$res) return 0;
		$msg = get_query("SELECT sender FROM Messages WHERE msgid=$msgid");
		$u = get_user($msg['sender']);
		if($u && $user['Name'] != $u['Name']) {
			send_tell($user['Name'], $u['userid'], "$user[Name] cancelled one of your messages!");
		}
		return 1;
	}

	function uncancel_message($msgid) {
		global $user;
		$res = mysql_query("UPDATE Messages SET Deleted=0 WHERE (msgid=$msgid) LIMIT 1");
		echo "uncancel res=$res<BR>";
		if(!$res) return 0;
		$msg = get_query("SELECT sender FROM Messages WHERE msgid=$msgid");
		$u = get_user($msg['sender']);
		if($u && $user['Name'] != $u['Name']) {
			send_tell($user['Name'], $u['userid'], "$user[Name] uncancelled one of your messages!");
		}
		return 1;
	}

	function ban_user($name) {
		$res = mysql_query("UPDATE Users SET Banned=1 WHERE Name='$name' LIMIT 1");
		if(!$res) return 0;
		return 1;
	}

	function get_last_read($uid, $board) {
		if(!$uid) return 0;
		if(!$board) $board="0";
		$x = get_query("SELECT msg FROM lastread WHERE (userid=$uid AND board=$board)");
		return $x['msg'];
	}
	
	function set_last_read($uid, $board, $msg) {
		if(!$uid) return;
		if(get_last_read($uid, $board)) {
			$res = mysql_query("UPDATE lastread SET msg=$msg WHERE (userid=$uid AND board=$board AND msg<$msg)");
		} else {
			$res = mysql_query("INSERT INTO lastread (userid, board, msg) VALUES ($uid, $board, $msg)");
		}
		//echo "$res<BR>";
	}
	
	function get_high_message($board) {
		if(!$board) $board="0";
		$msg = get_query("SELECT msgid FROM messages WHERE (boardid=$board AND Deleted=0) ORDER BY Timesent DESC LIMIT 1");
		return $msg['msgid'];
	}
	
	function load_group($id) {
		$sql = sprintf("SELECT * FROM groups WHERE GroupID=%d", $id);
		$usergroup = get_query($sql);
		return $usergroup;
	}
	
	function get_boards_at_loc($x, $y) {
		$sql = sprintf("SELECT * FROM Boards WHERE (LocX=%d AND LocY=%d AND Destroyed=0)", $x, $y);
		return get_query_array($sql);
	}
	
	function get_users_at_loc($x, $y) {
		$sql = sprintf("SELECT * FROM Users WHERE (LocX=%d AND LocY=%d AND Banned=0)", $x, $y);
		return get_query_array($sql);
	}
	
	function get_messages($boardid, $time=0, $limit=0) {
		$where = sprintf("boardid=%d", $boardid);
		if(is_int($time) && $time > 0) {
			$time = date("Y-m-d H:i:s", $time);
		}
		if(is_string($time)) {
			$where .= sprintf(" AND Timesent > '%s'", $time);
		}
		if($limit) {
			$limit = sprintf("LIMIT %d", $limit);
		}
		$sql = "SELECT * FROM Messages WHERE ($where) ORDER BY Timesent DESC $limit";
		return get_query_array($sql);
	}
	
	function get_events($logins=0, $limit=0) {
		if(!$logins) $where = "WHERE (EVENT NOT LIKE '%%logged%%')";
		if($limit) $limits = "LIMIT $limit";
		$sql = "SELECT * FROM EventLog $where ORDER BY Timesent DESC $limits";
		return get_query_array($sql);
	}
?>
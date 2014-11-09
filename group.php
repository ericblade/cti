<?PHP
	require_once('cti.php');
		$warezname = "Group";
	$version = "1.0";

	if($_POST['action']) $_GET['action'] = $_POST['action'];
	switch($_GET['action']) {
		case "CreateGroup":
			create_group();
			break;
		case "RemoveGroup":
			$TITLE = "Remove User Group";
			remove_group();
			break;
		case "AddUser":
			$TITLE = "Add User to Group";
			add_user();
			break;
		case "RemoveUser":
			$TITLE = "Remove User from Group";
			remove_user();
			break;
		case "TellAll":
			$TITLE = "Tell All In Group";
			tell_all();
			break;
		case "Change":
			$TITLE = "Change Group Settings";
			change_group();
			break;
		case "FingerGroup":
			$TITLE = "User Group Information";
			finger_group();
			break;
		default:
			$TITLE = "User Group Menu";
			//display_menu();
			break;
	}
	display_menu();
	
	require_once('end.php');
	
	function display_menu() {
		echo "<table>";
		echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?action=CreateGroup">CreateGroup</a></td><td>Create User Group</td></tr>';
		echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?action=RemoveGroup">RemoveGroup</a></td><td>Delete User Group</td></tr>';
		echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?action=AddUser">AddUser</a></td><td>Add User To Group</td></tr>';
		echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?action=RemoveUser">RemoveUser</a></td><td>Remove a User from your Group</td></tr>';
		echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?action=TellAll">TellAll</a></td><td>Send a Tell to all in your group</td></tr>';
		echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?action=Change">Change</a></td><td>Change User Group Attributes</td></tr>';
		echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?action=FingerGroup">FingerGroup</a></td><td>Obtain information about another Group</td></tr>';
		echo '</table>';
	}
	
	function create_group() {
		global $user;
		if($user['GroupID'] > 0) {
			echo "You are already in a group.<BR>";
		} else {
			if(!$_POST['groupname'] || $_POST['deny']) {
				echo 'This operation will cost you 200 credits.<P>';
				echo '<fieldset><legend>Create UserGroup</legend>';
				echo '<form name="creategroup" method="post" action="'.$_SERVER['PHP_SELF'].'">';
				echo '<p>';
				echo 'UserGroup Name:<input type="text" class="text" name="groupname" value="'.$_POST['groupname'].'"><p>';
				echo '<input type="hidden" name="action" value="CreateGroup">';
				echo '<input type="submit" value="Create">';
				echo '</form></fieldset>';
				return;
			} else {
				$groupname = substr(censor_text(fix_text($_POST['groupname'])), 0, 64);
				//print_r($_POST);
				if(!$_POST['confirm']) {
					printf("You wish to create a group called '%s', correct?", $groupname);
					echo '<form name="confirm" action="'.$_SERVER['PHP_SELF'].'" method="post"><p>';
					echo '<input type="hidden" name="action" value="CreateGroup">';
					echo '<input type="submit" name="confirm" value="Yes">';
					echo '<input type="submit" name="deny" value="No">';
					echo '<input type="hidden" name="groupname" value="'.$groupname.'">';
					echo '</form>';
					return;
				} else {
					$sql = sprintf("SELECT * FROM groups WHERE Name='%s'", $_POST['groupname']);
					$existing = get_query($sql);
					if($existing) {
						echo "There already seems to be a group with the name '".$_POST['groupname']."'.<p>";
						//display_menu();
						return;
					}
					$sql = sprintf("INSERT INTO groups (Name, CreatorUserID) VALUES ('%s', %d)", $_POST['groupname'], $user['userid']);
					$res = mysql_query($sql);
					if(!$res) {
						echo "Error creating group.<BR>";
						return;
					}
					printf("UserGroup '%s' created.<br>", $_POST['groupname']);
					$sql = sprintf("SELECT GroupID FROM groups WHERE (Name='%s')", $_POST['groupname']);
					$id = get_query($sql);
					$user['GroupID'] = $id['GroupID'];
					$sql = sprintf("UPDATE Users SET GroupID=%d WHERE userid=%d LIMIT 1", $id['GroupID'], $user['userid']);
					mysql_query($sql);
					//$user['Stamina'] -= 200;
					printf("'%s' assigned to UserGroup '%s'.<p>", $user['Name'], $_POST['groupname']);
					log_event(sprintf("%s created a new UserGroup, called %s.", $user['Name'], $_POST['groupname']));
					//display_menu();
					broadcast("Something in the Network has changed.");
				}
			}
		}
	}
	
	function remove_group() {
		global $user;
		if($user['GroupID'] == 0) {
			echo "You are not a member of a group, therefore you cannot remove a group.<BR>";
			echo "Either create a group, or find someone who has a group to join.<BR>";
		} else {
			$group = load_group($user['GroupID']);
			if(!$group) {
				echo "Error loading group information!<BR>";
				return;
			} else {
				if($group['CreatorUserID'] != $user['userid']) {
					echo "You did not create this group, therefore you cannot delete this group.<BR>";
					// TODO: implement group admins later
					return;
				}
				if(!$_POST['confirm']) {
					echo "You wish to remove your UserGroup.  This will remove all users from the group, at a cost of 200 credits.  Are you sure?";
					echo '<form name="confirm" method="post" action="'.$_SERVER['PHP_SELF'].'"><p>';
					echo '<input type="submit" name="confirm" value="Yes">';
					echo '<input type="hidden" name="action" value="RemoveGroup">';
					echo '</form>';
					return;
				} else {
					$sql = sprintf("SELECT userid FROM Users WHERE GroupID=%d", $group['GroupID']);
					$users = get_query($sql);
					foreach($users as $u) {
						if($u['userid'] != $user['userid'] && $u['GroupID'] == $group['GroupID'])
							send_tell("", $u['userid'], "Your UserGroup has been removed.");
					}
					$sql = sprintf("UPDATE Users SET GroupID=0 WHERE GroupID=%d", $group['GroupID']);
					mysql_query($sql);
					echo 'All users removed from UserGroup.<p>';
					log_event(sprintf("%s has disbanded the UserGroup '%s'", $user['Name'], $group['Name']), 0);
					$user['Stamina'] -= 200;
				}
			}
		}
	}
	
	function add_user() {
		global $user;
		if($user['GroupID'] == 0) {
			echo "You are not a member of a group, therefore you cannot add a user to your group.<BR>";
			echo "Either create a group, or find someone who has a group to join.<BR>";			
		} else {
			$group = load_group($user['GroupID']);
			if(!$group) {
				echo "Error loading group information!<BR>";
				return;
			} else {
				if($group['CreatorUserID'] != $user['userid']) {
					echo "You did not create this group, therefore you cannot add someone to it.<BR>";
					return;
				}
				if(!$_POST['username']) {
					echo '<fieldset><legend>Add User to UserGroup</legend>';
					echo "Confirmation for this operation will be implemented in the near future. Please make sure that this user wishes to be in your UserGroup.<p>";
					echo '<form name="adduser" method="post" action="'.$_SERVER['PHP_SELF'].'"><p>';
					echo 'Enter name to add:<input type="text" class="text" name="username">';
					echo '<input type="hidden" name="action" value="AddUser">';
					echo '<p><input type="submit" value="Add User"></form></fieldset>';
					return;
				} else {
					$u = get_user($_POST['username']);
					if(!$u) {
						echo "Unable to find user '".$_POST['username']."' to add.<p>";
						return;
					}
					if($u['GroupID'] > 0) {
						printf("%s is already in a UserGroup. They will need to be removed from their existing group first.<p>", $u['Name']);
						return;
					}
					$u['GroupID'] = $user['GroupID'];
					$sql = sprintf("UPDATE users SET GroupID=%d WHERE userid=%d LIMIT 1", $user['GroupID'], $u['userid']);
					mysql_query($sql);
					send_tell($user['Name'], $u['userid'], "$user[Name] has added you to UserGroup $group[Name].");
					echo "User added.<p>";
				}
			}
		}
	}
	
	function remove_user() {
		global $user;
		if($user['GroupID'] > 0) {
			echo "You are not a member of a group, therefore you cannot remove a group.<BR>";
			echo "Either create a group, or find someone who has a group to join.<BR>";
		} else {
			$group = load_group($user['GroupID']);
			if(!$group) {
				echo "Error loading group information!<BR>";
				return;
			} else {
				if($group['CreatorUserID'] != $user['userid']) {
					echo "You did not create this group, therefore you cannot remove someone from it.<BR>";
					return;
				}
				echo "Group Remove not yet implemented.<BR>";
			}
		}
	}
	
	function tell_all() {
		global $user;
		if($user['GroupID'] > 0) {
			echo "You are not a member of a group, therefore you cannot tell to your group.<BR>";
			echo "Either create a group, or find someone who has a group to join.<BR>";
		} else {
			echo "Group Tell not yet implemented.<BR>";
		}
	}
	
	function change_group() {
		global $user;
		if($user['GroupID'] > 0) {
			echo "You are not a member of a group, therefore you cannot change your group's settings.<BR>";
			echo "Either create a group, or find someone who has a group to join.<BR>";
		} else {
			$group = load_group($user['GroupID']);
			if(!$group) {
				echo "Error loading group information!<BR>";
				return;
			} else {
				if($group['CreatorUserID'] != $user['userid']) {
					echo "You did not create this group, therefore you cannot change it.<BR>";
					return;
				}
				echo "Group Change not yet implemented.<BR>";
			}
		}
	}
	
	function finger_group() {
		echo "Group Finger not yet implemented.<br>";
		return;
	}

?>
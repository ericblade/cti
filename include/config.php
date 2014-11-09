<?PHP
	require_once('database.php'); // this SHOULD already be included in anything that includes us, but just in case
	// game config variables, and functions to deal with the GameSettings database table
	// TODO: Probably want to just move all the variables over to the GameSettings database, or find some other way to localise everything.. but oh well
	// That might be a lot more work than I'm willing and able to do for this
	$USETIMELIMITS		= False;
	// TODO: Someone should test with UseTimeLimits = True and see if that function still works
	
	$PLAYERNAME			= "User";
	$PLAYERNAMEPLURAL	= "Users";
	
	$LEVELNAME			= "Access Level";
	$LEVELNAMEPLURAL	= "Access Levels";
	
	$STAMINANAME		= "credit";
	$STAMINANAMEPLURAL	= "credits";
	
	$WALLETNAME			= "credchip";
	$WALLETNAMEPLURAL	= "credchips";
	
	$HOMENAME			= "Home Node";
	$ORIGINNAME			= "Primary Access Point";
	$LOCATIONNAME		= "Node";
	
	$BOARDNAME			= "Board";
	$BOARDNAMEPLURAL	= "Boards";
	$MESSAGENAME		= "Message";
	$MESSAGENAMEPLURAL	= "Messages";
	
	$UNREADHERE			= "<P>*** There are unread $MESSAGENAMEPLURAL here.<BR>";
	$LOCBOARDSHERE		= "<P>*** There are special $BOARDNAMEPLURAL at this $LOCATIONNAME.<BR>";
	
	$PLAYERSHERE		= '<div class="windowtitle">Other '.$PLAYERNAMEPLURAL.' Present</div>';

	$OVERLOADEDSTAMINA	= "Your $WALLETNAME is overloaded. %d $STAMINANAMEPLURAL are being returned.<BR>";
	$ALLOCATEDSTAMINA	= "*** Allocated %d credits.<BR>";
	
	$TIMEEXPIRED		= "Your time for this session has expired.  Higher $LEVELNAMEPLURAL may stay longer.";
	
	$NOACCESSTOLOC		= " *** Error: Unable to access current $LOCATIONNAME.  Returning to $HOMENAME.<BR>";
	$NOACCESSTOHOME		= " *** Error: $HOMENAME inaccessible.  Returning to $ORIGINNAME.<BR>";

	$ACTIVITYHEADER		= '<div class="windowtitle">Recent Network Activity</div>';
	$TELLHEADER		= '<div class="windowtitle">'.$MESSAGENAMEPLURAL.' for you</div>';	
	
	$BN_MOTD			= -3;	// MOTD Board number
	$MOTDHEADER		= '<div class="windowtitle">Message%s of the Day</div>';
	
	$INPUTJS = 'onfocus="this.style.border=\'2px solid black\';" onblur="this.style.border=\'\';"';
	// This should be added to all INPUT fields that you want to be highlighted when they are focused.

	require_once('database.php');
	
	function AdjustStaminaPool($amount) {
		$sql = sprintf("UPDATE GameSettings SET StaminaPool=StaminaPool+%d", $amount);
		mysql_query($sql);
		// TODO: Log this if there's an error?
	}
	
	function QueryStaminaPool() {
		$sql = "SELECT StaminaPool from GameSettings";
		$pool = get_query($sql);
		$pool = $pool['StaminaPool'];
		return $pool;
	}
?>
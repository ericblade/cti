<?PHP
	require_once('cti.php');
	
	$sql = "SELECT LastMaintTime FROM GameSettings";
	$lmt = get_query($sql);
	$lmt = $lmt['LastMaintTime'];
	
	echo "Last maintenace run: " . date('l dS \of F Y h:i:s A', $lmt). "<BR>";
	
	require_once('end.php');
?>

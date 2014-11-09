<?php
	$warezname = "Intro";
	$version = "1.0";
	require_once('cti.php');
	echo "<h1>Welcome to CTI</h1>";
	$_SESSION['command'] = "";
	//echo '<img src="images/ctilogo1.jpg" style="position:absolute; top: 0; left: 0; bottom: 0; right: 0; filter:alpha(opacity=25); opacity: 0.25; -moz-opacity:0.25; z-index: -5;">';
	//echo '<img src="http://www.wishingline.com/blogimages/happy_holidays.jpg" style="width:100%; position:absolute; top: 0; left: 0; bottom: 0; right: 0; filter:alpha(opacity=25); opacity: 0.25; -moz-opacity:0.25;">';
	echo "All that is visible must grow beyond itself, and extend into the realm of the invisible.<p>";
/*echo '
For all of those United States citizens:<P>
<img src="http://steelturman.typepad.com/photos/uncategorized/vote_or_else1.gif">
<p>';*/
	display_main();
	display_location_info();
	//echo '<p><a href="http://www.freedomain.co.nr/">
//<img src="http://eesaora.4u.com.ru/coimage.gif" width="88" height="31" alt="Free Domain Name - www.YOU.co.nr!" style="border: 0px;" /></a></p>';
	require_once('end.php');
?>

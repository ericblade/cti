<?php
	require_once('cti.php');
	
	// we originally ran it with rand(20, 256) ..
	//for($i = 0; $i < 20; $i++) {
		$x = rand(10, 20);
		$y = rand(10, 20);
		mysql_query("INSERT INTO atms (locx, locy, credits) VALUES ($x, $y, 20)");
		mysql_query("INSERT INTO specials (locx, locy, 'atm.php', 'ATM'");
		echo "Placing ATM at Node ($x, $y)<BR>";
	//}
	//for($i = 0; $i < 20; $i++) {
		$x = rand(-10, -20);
		$y = rand(-10, -20);
		mysql_query("INSERT INTO atms (locx, locy, credits) VALUES ($x, $y, 20)");
		mysql_query("INSERT INTO specials (locx, locy, 'atm.php', 'ATM'");
		echo "Placing ATM at Node ($x, $y)<BR>";
	//}
	//for($i = 0; $i < 20; $i++) {
		$x = rand(10, 20);
		$y = rand(-10, -20);
		mysql_query("INSERT INTO atms (locx, locy, credits) VALUES ($x, $y, 20)");
		mysql_query("INSERT INTO specials (locx, locy, 'atm.php', 'ATM'");
		echo "Placing ATM at Node ($x, $y)<BR>";
	//}
	//for($i = 0; $i < 20; $i++) {
		$x = rand(-10, -20);
		$y = rand(10, 20);
		mysql_query("INSERT INTO atms (locx, locy, credits) VALUES ($x, $y, 20)");
		mysql_query("INSERT INTO specials (locx, locy, 'atm.php', 'ATM'");
		echo "Placing ATM at Node ($x, $y)<BR>";
	//}
	require_once('end.php');
?>
<?php
	require_once('cti.php');
	for($i = 0; $i < 100; $i++) {
		if($i > 50) $x1 = 0;
		else $x1 = rand(-10,10);
		if($i < 50) $y2 = 0;
		else $y2 = rand(-10,10);
		$y1 = rand(-10,10);
		$x2 = rand(-10,10);
		//$y2 = rand(-1024, 1024);
		printf("(%d,%d) = same sector as (%d, %d): ", $x1, $y1, $x2, $y2);
		$z = same_sector($x1, $y1, $x2, $y2);
		if($z) echo "Yes";
		else echo "No";
		echo "<BR>";
	}
	require_once('end.php');
?>
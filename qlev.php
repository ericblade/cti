<?PHP
	require_once('cti.php');
	$qs = get_query_array("SELECT qid,level FROM questions ORDER BY level");
	$ql = $qs[0]['level'];
	if($ql < 0) $ql = 1;
	foreach($qs as $q) {
		$ql += 1;
		if($ql > 192)
			$ql = 1;
		$sql = sprintf("UPDATE questions SET level=%d WHERE qid=%d", $ql, $q['qid']);
		mysql_query($sql);
	}
	require_once('end.php');
?>
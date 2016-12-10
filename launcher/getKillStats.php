<?php

if($_POST && isset($_POST['key']) && $_POST['key'] == '3TcXN9rR2vxk8qj38388tujDTRJX2C3s9M3Cug') {
	try {
	  	$stats = new PDO("mysql:host=127.0.0.1;dbname=KillStats", "KillStats", "mweKAKrahW8Uz8RZ");
	  	$stats->exec("SET NAMES utf8");

	  	$stats = $stats->query('SELECT * FROM killstats_data');
	  	$stats = $stats->fetchAll();

	} catch (Exception $e) {
		header('HTTP/1.1 500 Internal Server Error');
	}

	foreach ($stats as $key => $value) {
		$stats[$value['playerName']] = $value;
		unset($stats[$value['playerName']]['playerName']);
		unset($stats[$value['playerName']]['playerID']);
		unset($stats[$value['playerName']]['ScoreboardEnabled']);
		foreach ($stats[$value['playerName']] as $k => $v) {
			if(is_int($k)) {
				unset($stats[$value['playerName']][$k]);
			}
		}
		unset($stats[$key]);
	}

	echo json_encode($stats);
} else {
	header('HTTP/1.0 404 Not Found');
    exit;
}
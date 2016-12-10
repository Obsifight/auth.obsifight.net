<?php
if($_POST && $_POST['key'] == 's4H2rZR4M73GTaerD46ah') {
	try {
	  	$bdd = new PDO("mysql:host=5.196.212.202;dbname=ObsiFight_AdminTools", "panel", "rwiQ3yNL8p982Y");
	  	$bdd->exec("SET NAMES utf8");

	} catch (Exception $e) {
		header('HTTP/1.1 500 Internal Server Error');
	}

	$logs_players = $bdd->query('SELECT * FROM BAT_players');
  	$logs_players = $logs_players->fetchAll();

  	$logs_kick = $bdd->query('SELECT * FROM BAT_kick ORDER BY kick_id DESC');
  	$logs_kick = $logs_kick->fetchAll();

  	$logs_mute = $bdd->query('SELECT * FROM BAT_mute ORDER BY mute_id DESC');
  	$logs_mute = $logs_mute->fetchAll();

  	$logs_bans = $bdd->query('SELECT * FROM BAT_ban ORDER BY ban_id DESC');
  	$logs_bans = $logs_bans->fetchAll();

	foreach ($logs_players as $key => $value) {
		$players[$value['UUID']] = $value['BAT_player'];
	}

	$data['players'] = $players;
	$data['kicks'] = $logs_kick;
	$data['bans'] = $logs_bans;
	$data['mutes'] = $logs_mute;

	echo json_encode($data);
} else {
	header('HTTP/1.0 404 Not Found');
    exit;
}
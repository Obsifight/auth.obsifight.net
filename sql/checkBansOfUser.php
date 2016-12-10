<?php
if($_POST && $_POST['key'] == 's4H2rZR4M73GTaerD46ah') {
	try {
	  	$bdd = new PDO("mysql:host=5.196.212.202;dbname=ObsiFight_AdminTools", "panel", "rwiQ3yNL8p982Y");
	  	$bdd->exec("SET NAMES utf8");

	} catch (Exception $e) {
    echo '500';
		header('HTTP/1.1 500 Internal Server Error');
    exit;
	}

  $user = (isset($_POST['user'])) ? $_POST['user'] : '';

	$getPlayer = $bdd->prepare('SELECT UUID FROM BAT_players WHERE BAT_player=:player');
  $getPlayer->execute(array('player' => $user));
  $getPlayer = $getPlayer->fetch();

  if(!empty($getPlayer)) {

  	$logs_bans = $bdd->prepare('SELECT * FROM BAT_ban WHERE UUID=:UUID');
    $logs_bans->execute(array('UUID' => $getPlayer['UUID']));
  	$logs_bans = $logs_bans->fetchAll();

    $ban = false;

  	foreach ($logs_bans as $key => $value) {
      if($value['ban_state'] == 1) {
        $ban = true;
        break;
      }
  	}

  	echo json_encode(array('ban' => $ban));

  } else {
    echo '500';
    header('HTTP/1.1 500 Internal Server Error');
    exit;
  }
} else {
  echo '404';
	header('HTTP/1.0 404 Not Found');
    exit;
}

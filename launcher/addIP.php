<?php
if($_POST && !empty($_POST['username']) && isset($_POST['key']) && $_POST['key'] == '3TcXN9rR2vxk8qj38388tujDTRJX2C3s9M3Cug') {
	try
	{
	    $bdd_server = new PDO("mysql:host=127.0.0.1;dbname=V4_launcher", "site", "wSR8wp6ur7fjyuf3");
	    $bdd_server->exec("SET NAMES utf8");
	}
	catch (Exception $e)
	{
	  	header('HTTP/1.1 500 Internal Server Error');
	}

	if(isset($_POST['ip']) && $_POST['ip'] != "desactivate") {
		if(empty($_POST['ip'])) {

			$ip = serialize(array());

			file_put_contents('/home/logs/obsiguard.log', file_get_contents('/home/logs/obsiguard.log')."\r[".date('Y-m-d H:i:s')."] Activation d'ObsiGuard chez l'utilisateur : ".$_POST['username']."");

		} else {
			$req = $bdd_server->prepare('SELECT authorised_ip FROM joueurs WHERE user_pseudo=:user_pseudo');
			$req -> bindParam(':user_pseudo', $_POST['username'], PDO::PARAM_STR);
			$req -> execute();

			$ip = $req->fetchAll();
			$ip = unserialize($ip[0]['authorised_ip']);
			if(!$ip) {
				$ip = array();
			}

			if(!in_array($_POST['ip'], $ip)) {
				$ip[] = $_POST['ip'];
			}
			$ip = serialize($ip);

			file_put_contents('/home/logs/obsiguard.log', file_get_contents('/home/logs/obsiguard.log')."\r[".date('Y-m-d H:i:s')."] (".$_POST['ip'].") Ajout d'IP d'ObsiGuard chez l'utilisateur : ".$_POST['username']."");
		}
	} else {
		$ip = null;

		file_put_contents('/home/logs/obsiguard.log', file_get_contents('/home/logs/obsiguard.log')."\r[".date('Y-m-d H:i:s')."] Désactivation d'ObsiGuard chez l'utilisateur : ".$_POST['username']."");
	}

	$req = $bdd_server->prepare('UPDATE joueurs SET authorised_ip=:authorised_ip WHERE user_pseudo=:user_pseudo');
	$req -> bindParam(':user_pseudo', $_POST['username'], PDO::PARAM_STR);
	$req -> bindParam(':authorised_ip', $ip, PDO::PARAM_STR);
	$req -> execute();

	echo 'done';
} else {
	header('HTTP/1.0 404 Not Found');
    exit;
}
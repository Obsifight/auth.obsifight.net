<?php
if($_POST && isset($_POST['username'])  && isset($_POST['key']) && $_POST['key'] == '3TcXN9rR2vxk8qj38388tujDTRJX2C3s9M3Cug') {
	try
	{
	    $bdd_server = new PDO("mysql:host=127.0.0.1;dbname=V4_launcher", "site", "wSR8wp6ur7fjyuf3");
	    $bdd_server->exec("SET NAMES utf8");
	}
	catch (Exception $e)
	{
	  	header('HTTP/1.1 500 Internal Server Error');
	}

	$req = $bdd_server->prepare('SELECT dynamic_ip FROM joueurs WHERE user_pseudo=:user_pseudo');
	$req->execute(array('user_pseudo' => $_POST['username']));
	$ip = $req->fetchAll()[0]['dynamic_ip'];

	if($ip) {
		$ip = 0;
	} else {
		$ip = 1;
	}

	$modif = ($ip) ? 'Activation' : 'Désactivation';

	file_put_contents('/home/logs/obsiguard.log', file_get_contents('/home/logs/obsiguard.log')."\r[".date('Y-m-d H:i:s')."] ".$modif." d'IP dynamique d'ObsiGuard chez l'utilisateur : ".$_POST['username']."");

	$req = $bdd_server->prepare('UPDATE joueurs SET dynamic_ip=:dynamic_ip WHERE user_pseudo=:user_pseudo');
	$req->execute(array('dynamic_ip' => $ip, 'user_pseudo' => $_POST['username']));

	echo 'done';
} else {
	header('HTTP/1.0 404 Not Found');
    exit;
}
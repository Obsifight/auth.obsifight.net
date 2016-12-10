<?php
if($_POST && !empty($_POST['username']) && !empty($_POST['ip']) && isset($_POST['key']) && $_POST['key'] == '3TcXN9rR2vxk8qj38388tujDTRJX2C3s9M3Cug') {

	try
	{
	    $bdd_server = new PDO("mysql:host=127.0.0.1;dbname=V4_launcher", "site", "wSR8wp6ur7fjyuf3");
	    $bdd_server->exec("SET NAMES utf8");
	}
	catch (Exception $e)
	{
	  	header('HTTP/1.1 500 Internal Server Error');
	}

	$req = $bdd_server->prepare('SELECT authorised_ip FROM joueurs WHERE user_pseudo=:user_pseudo');
	$req -> execute(array('user_pseudo' => $_POST['username']));

	$ip = $req->fetchAll();
	$ip = unserialize($ip[0]['authorised_ip']);

	if(is_array($ip)) {
		foreach ($ip as $key => $value) {
			if($value == $_POST['ip']) {
				unset($ip[$key]);
				break;
			}
		}
	} else {
		$ip = array();
	}

	file_put_contents('/home/logs/obsiguard.log', file_get_contents('/home/logs/obsiguard.log')."\r[".date('Y-m-d H:i:s')."] (".$_POST['ip'].") Supression d'IP d'ObsiGuard chez l'utilisateur : ".$_POST['username']."");

	$ip = serialize($ip);

	$req = $bdd_server->prepare('UPDATE joueurs SET authorised_ip=:authorised_ip WHERE user_pseudo=:user_pseudo');
	$req -> execute(array('user_pseudo' => $_POST['username'], 'authorised_ip' => $ip));

	echo 'done';
} else {
	header('HTTP/1.0 404 Not Found');
    exit;
}
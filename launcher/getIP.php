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

	$req = $bdd_server->prepare('SELECT authorised_ip,dynamic_ip FROM joueurs WHERE user_pseudo=:user_pseudo');
	$req -> execute(array('user_pseudo' => $_POST['username']));

	$ip = $req->fetchAll();
	$ip['authorised_ip'] = unserialize($ip[0]['authorised_ip']);
	$ip['dynamic_ip'] = $ip[0]['dynamic_ip'];

	echo json_encode($ip);
} else {
	header('HTTP/1.0 404 Not Found');
    exit;
}
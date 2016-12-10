<?php
if($_POST && isset($_POST['player']) && $_POST['key'] == 's4H2rZR4M73GTaerD46ah') {
	try
	{
	    $bdd_server = new PDO("mysql:host=127.0.0.1;dbname=V4_launcher", "site", "wSR8wp6ur7fjyuf3");
	    $bdd_server->exec("SET NAMES utf8");
	}
	catch (Exception $e)
	{
	  	header('HTTP/1.1 500 Internal Server Error');
	}

	$req = $bdd_server->prepare('SELECT * FROM joueurs WHERE user_pseudo=:user_pseudo');
	$req -> bindParam(':user_pseudo', $_POST['player'], PDO::PARAM_STR);
	$req -> execute();
	$data = $req->fetch();

	$data['obsiguard'] = (empty($data['authorised_ip'])) ? false : true;
	$data['dynamic_ip'] = ($data['dynamic_ip']) ? true : false;
	$data['ip'] = (empty($data['authorised_ip'])) ? false : implode(', ', unserialize($data['authorised_ip']));

	echo json_encode($data);
} else {
	header('HTTP/1.0 404 Not Found');
    exit;
}
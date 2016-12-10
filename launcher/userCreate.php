<?php
include 'UUID.php';

if($_POST && !empty($_POST['username']) && !empty($_POST['password']) && isset($_POST['key']) && $_POST['key'] == '3TcXN9rR2vxk8qj38388tujDTRJX2C3s9M3Cug') { // Grosse securité eywek gg
	try
	{
	    $bdd_server = new PDO("mysql:host=127.0.0.1;dbname=V4_launcher", "site", "wSR8wp6ur7fjyuf3");
	    $bdd_server->exec("SET NAMES utf8");
	}
	catch (Exception $e)
	{
	  	header('HTTP/1.1 500 Internal Server Error');
	}

	$req = $bdd_server->prepare('INSERT INTO joueurs(user_pseudo, user_mdp, profileid) VALUES(:user_pseudo, :user_mdp, :profileid)');
	$req -> bindParam(':user_pseudo', $_POST['username'], PDO::PARAM_STR);
	$req -> bindParam(':user_mdp', $_POST['password'], PDO::PARAM_STR);
	$uuid = UUID::v4();
	$req -> bindParam(':profileid', $uuid , PDO::PARAM_STR);
	$req -> execute();

	echo 'done';
} else {
	header('HTTP/1.0 404 Not Found');
    exit;
}


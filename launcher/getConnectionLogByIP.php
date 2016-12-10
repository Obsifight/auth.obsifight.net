<?php
if($_POST && !empty($_POST['ip']) && isset($_POST['key']) && $_POST['key'] == '3TcXN9rR2vxk8qj38388tujDTRJX2C3s9M3Cug') {
	try {
	  	$bdd_launcher = new PDO("mysql:host=127.0.0.1;dbname=V4_utils", "utils", "jzp8pVp2SUcLJjEq");
	  	$bdd_launcher->exec("SET NAMES utf8");

	  	$logs_launcher = $bdd_launcher->prepare('SELECT * FROM loginlogs WHERE ip=:ip ORDER BY id DESC');
	  	$logs_launcher->execute(array('ip' => $_POST['ip']));
	  	$logs_launcher = $logs_launcher->fetchAll();

	} catch (Exception $e) {
		header('HTTP/1.1 500 Internal Server Error');
	}

	echo json_encode($logs_launcher);
} else {
	header('HTTP/1.0 404 Not Found');
    exit;
}
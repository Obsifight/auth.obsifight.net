<?php

/*
  On check le spam

  3 MAC toutes les 30m
  1 toutes les 10m
*/

$ip = $_SERVER['REMOTE_ADDR'];

// On cherche si y'a eu un essai il y a moins de 10 minutes de la même IP
$findIPTry = Core\Queries::execute(
    'SELECT `id` FROM `mac_adress_try` WHERE `time` < :now AND `ip`=:ip',
  [
    'now' => strtotime('+10 minutes'),
    'ip' => $ip,
  ]
);

if(empty($findIPTry)) {
  Core\Queries::execute(
      'DELETE FROM `mac_adress_try` WHERE `ip`=:ip',
    [
      'ip' => $ip,
    ]
  );
  Core\Queries::execute(
      'INSERT INTO `mac_adress_try`(`id`, `ip`, `time`) VALUES (\'\',:ip,:now)',
    [
      'now' => time(),
      'ip' => $ip,
    ]
  );
} else {
  exit('Nope.');
}

/*
  =====
*/


$macAdress = isset($URLargs[2]) ? $URLargs[2] : null;
$username = isset($URLargs[1]) ? $URLargs[1] : null;

if(empty($macAdress) || empty($username)) {
  echo json_encode(array(
    'error' => "IllegalArgumentException",
    'errorMessage' => "macAdress or username are empty."
  ));
  exit;
}

$query = Core\Queries::execute(
    'UPDATE joueurs SET mac_adress=:adress WHERE user_pseudo=:username',
  [
    'adress' => $macAdress,
    'username' 	  => $username,
  ]
);

echo json_encode(array('success' => true));

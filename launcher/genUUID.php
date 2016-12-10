<?php
include 'UUID.php';

try
{
    $bdd_server = new PDO("mysql:host=127.0.0.1;dbname=V4_launcher", "site", "wSR8wp6ur7fjyuf3");
    $bdd_server->exec("SET NAMES utf8");
}
catch (Exception $e)
{
    header('HTTP/1.1 500 Internal Server Error');
}

$findAllUsers = $bdd_server->query('SELECT * FROM joueurs WHERE profileid = \'\'');

$i = 0;
foreach ($findAllUsers as $key => $value) {

  $req = $bdd_server->prepare('UPDATE joueurs SET profileid=:uuid WHERE user_pseudo=:pseudo');
  $req -> bindParam(':pseudo', $value['user_pseudo'] , PDO::PARAM_STR);
  $uuid = UUID::v4();
  $req -> bindParam(':uuid', $uuid , PDO::PARAM_STR);
  $req -> execute();

  $i++;

}

echo 'done';
echo '<br>Result : '.$i.' players edited.';

?>

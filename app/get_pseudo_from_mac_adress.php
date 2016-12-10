<?php

$macAdress = isset($URLargs[1]) ? $URLargs[1] : null;

if(empty($macAdress)) {
  echo json_encode(array(
    'error' => "IllegalArgumentException",
    'errorMessage' => "macAdress is empty."
  ));
  exit;
}

$query = Core\Queries::execute(
    'SELECT user_pseudo FROM joueurs WHERE mac_adress=:adress',
  [
    'adress' => $macAdress
  ]
);

$accounts = array();
if(!empty($query)) {

  if(is_array($query)) {
    foreach ($query as $user) {
      $accounts[] = $user->user_pseudo;
    }
  } else {
    $accounts[] = $query->user_pseudo;
  }

}

echo json_encode(array('success' => true, 'accounts' => $accounts));

<?php

$user_pseudo = isset($URLargs[1]) ? $URLargs[1] : null;

if(empty($user_pseudo)) {
  echo json_encode(array(
    'error' => "IllegalArgumentException",
    'errorMessage' => "pseudo is empty."
  ));
  exit;
}

$query = Core\Queries::execute(
    'SELECT mac_adress FROM joueurs WHERE user_pseudo=:user_pseudo',
  [
    'user_pseudo' => $user_pseudo
  ]
);

if(!empty($query)) {
  echo json_encode(array('success' => true, 'adress' => $query->mac_adress));
} else {
  echo json_encode(array('success' => true, 'adress' => null));
}

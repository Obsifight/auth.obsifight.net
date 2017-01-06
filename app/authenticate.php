<?php
/*
* Copyright 2015 TheShark34 & Vavaballz & Eywek
*
* This file is part of OpenAuth.

* OpenAuth is free software: you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* OpenAuth is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public License
* along with OpenAuth.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Verify if a user exist and if he had right credentials
 *
 * @param $username
 *            The username of the user
 * @param $password
 *            The password of the user
 * @return bool
 *            True if yes, false if not
 */
function auth($username, $password, $adressesMac) {
	// Sending the request to the database
	$req = Core\Queries::execute("SELECT * FROM joueurs WHERE user_pseudo = :username", ['username' => $username]);

	// If the request found a user
	if(isset($req) && !empty($req)) {
		// Hashing the given password
		$salt = 'PApVSuS8hDUEsOEP0fWZESmODaHkXVst27CTnYMM';
        $password = sha1($username.$salt.$password);

		// If it is the same as the one of the database
		if($password == $req->user_mdp) {
			if(!empty($req->authorised_ip)) { // si obsiguard est activé
                $ip = unserialize($req->authorised_ip); // on prend les IPs autorisées
            }

            if($req->dynamic_ip) { // si une ip est dynamique

                if(!empty($ip) && is_array($ip)) { // si obsiguard est activé et que les IPs sont pas vides

                    foreach ($ip as $key => $value) { // on les parcours pour récupérés leurs plages d'IP

                        $ip_range = explode('.', $value);
                        $last_ip_range = $ip_range;
                        end($last_ip_range);
                        unset($ip_range[key($last_ip_range)]);
                        $ip[$key] = implode('.', $ip_range);

                        unset($ip_range);
                        unset($last_ip_range);
                    }
                }

                // On récupére la plage d'IP du type
                $ip_range = explode('.', $_SERVER['REMOTE_ADDR']);
                $last_ip_range = $ip_range;
                end($last_ip_range);
                unset($ip_range[key($last_ip_range)]);
                $ip_range = implode('.', $ip_range);
            }

      if(empty($req->authorised_ip) || (!$req->dynamic_ip && in_array($_SERVER['REMOTE_ADDR'], $ip)) || ($req->dynamic_ip && in_array($ip_range, $ip))) { // Si ObsiGuard est désactivé ou que l'IP est autorisée et qu'elle est pas config dynamique. OU que l'ip est config comme dynamique et que la plage IP est autorisée
        $query = Core\Queries::execute("SELECT id FROM mac_adresses_banned WHERE adress = '" . explode("' AND adress = '", $adressesMac) . "'", []);
        if ($query && !empty($query))
          return array(false, 'mac', $query->adress);
				// Returning true
				return array(true, $req->user_id, null);
			} else {

				// On envoie l'IPN au site
				/*$domain = 'http://obsifight.net';
				//$domain = 'http://dev.obsifight.eywek.fr';
				$url = $domain.'/obsiapi/ipn/obsiguard/'.$req->user_pseudo.'/'.$_SERVER['REMOTE_ADDR'];
				@file_get_contents($url, false, stream_context_create(array('http'=>array('timeout' => 2)))); // 2 secondes MAX*/

				return array(false, 'ip', null);
			}

		// Else if the password aren't the same
		} else {
			// Returning false
			return array(false, false, null);
		}

	}

	// Else if the request didn't find an user
	else
		// Returning false
		return false;
}

/**
 * Send a response with the agent
 *
 * @param $username
 *            The username of the user
 * @param $clientToken
 *            The client token
 * @param $agentName
 *            The name of the agent
 * @param $agentVersion
 *            The version of the agent
 */
function send_response_agent($username, $clientToken, $agentName, $agentVersion) {
	// Generating a random access token
	$accessToken = md5(uniqid(rand(), true));

	// Sending a request to the database to get the user
	$req = Core\Queries::execute("SELECT * FROM joueurs WHERE user_pseudo = :username", ['username' => $username]);

	// Getting the user UUID
	$playerUUID = $req->profileid;

	// If the given client token is empty
	if(empty($clientToken)) {
		// Generating a new client token
		$newClientToken = getClientToken(32);

		// Sending a request to the database to save the access token and the client token
		Core\Queries::execute(
	  		'UPDATE joueurs SET access_token=:accessToken, has_connected_v6=1 WHERE user_pseudo=:username',
			[
				'accessToken' => $accessToken,
				'username' 	  => $username,
			]
		);

		// Creating an array of the result
		$result = [
			'accessToken' => $accessToken,
			'clientToken' => "",
			'availableProfiles' => [
				[
					'id' => $playerUUID,
					'name' => $username
				]
			],
			'selectedProfile' => [
				'id' => $playerUUID,
				'name' => $username
			]
		];

		// Creating the JSON by the result array
		$result = json_encode($result);

		// Printing the JSON result
		echo $result;
	}

	// Else if the client token isn't empty
	else {
		// Sending a request to the database to save the access token
		Core\Queries::execute(
			'UPDATE joueurs SET access_token=:accessToken, has_connected_v6=1 WHERE user_pseudo=:username',
			[
				'accessToken' => $accessToken,
				'username' 	  => $username,
			]
		);

		// Creating an array of the result
		$result = [
			'accessToken' => $accessToken,
			//'clientToken' => $clientToken,
			'availableProfiles' => [
				[
					'id' => $playerUUID,
					'name' => $username
				]
			],
			'selectedProfile' => [
				'id' => $playerUUID,
				'name' => $username
			]
		];

		// Creating the JSON by the result array
		$result = json_encode($result);

		// Printing the JSON result
		echo $result;
	}
}

/**
 * Return the response without the agent
 *
 * @param $username
 *            The username of the user
 * @param $clientToken
 *            The client token
 */
function send_response($username, $clientToken){
	// Generating a random access token
	$accessToken = md5(uniqid(rand(), true));

	// If the client token is empty
	if(empty($clientToken)) {
		// Generating a new client token
		$newClientToken = getClientToken();

		// Sending a request to the database to save the new access and client tokens
		Core\Queries::execute(
			"UPDATE joueurs SET access_token=:accessToken, clientToken=:clientToken, has_connected_v6=1 WHERE user_pseudo=:username",
			[
				'accessToken' => $accessToken,
				'clientToken' => $newClientToken,
				'username'	  => $username
			]
		);

		// Creating a response array
		$response = array(
			'accessToken' => $accessToken,
			'clientToken' => $newClientToken
		);

		// Generating a JSON of the response
		$result = json_encode($response);

		// Printing it
		echo $result;
	}

	// Else if the client token isn't empty
	else {
		// Sending a request to the database to update the access token
		Core\Queries::execute(
			"UPDATE joueurs SET access_token=:accessToken, has_connected_v6=1 WHERE user_pseudo=:username",
			[
				'accessToken' => $accessToken,
				'username'	  => $username
			]
		);

		// Creating a response array
		$response = array(
			'accessToken' => $accessToken,
			'clientToken' => $clientToken
		);

		// Generating a JSON of it
		$result = json_encode($response);

		// Printing it
		echo $result;
	}
}

// If the request method is POST
if($request['method'] == "POST") {
	// If the content-type is JSON

	if(strpos($request['content-type'], "application/json") !== FALSE){
		// Getting the sent content
		$input = file_get_contents("php://input");

		// Parsing the JSON
		$getContents = json_decode($input, true);

		// Getting the username, the password, the client token, and the agent
		$username = isset($getContents['username']) ? $getContents['username'] : null;
		$password = isset($getContents['password']) ? $getContents['password'] : null;
		$clientToken = isset($getContents['clientToken']) ? $getContents['clientToken'] : null;
		$agent = isset($getContents['agent']) ? $getContents['agent'] : null;
    $adressesMac = isset($getContents['mac_adresses']) ? $getContents['mac_adresses'] : array();

		// If the authentication worked
		list($try, $other, $customMessage) = auth($username, $password, $adressesMac);
		if($try === true) {
      $user_id = $other;

			// log into log database
	        Core\Queries::execute(
				"INSERT INTO loginlogs(username, ip, type) VALUES(:username, :ip, :type)",
				[
					'username' => $username,
					'ip'	  => $_SERVER['REMOTE_ADDR'],
					'type'    => 'launcher'
				]
			, 'log');

      // log mac
      foreach ($adressesMac as $adress) {
        Core\Queries::execute(
        "INSERT INTO mac_adresses(adress, user_id, login_date) VALUES(:adress, :user_id, :login_date)",
        [
          'adress' => $adress,
          'user_id' => $user_id,
          'login_date' => date('Y-m-d H:i:s')
        ]);
      }

			// If the agent field isn't null
			if(!is_null($agent))
				// Sending a response with the agent
				send_response_agent($username, $clientToken, $agent['name'], $agent['version']);

			// Else if the agent field is null
			else
				// Sending a response without the agent
				send_response($username, $clientToken);
		} elseif($try === false && $other === 'ip') {
			// Else returning the third-half error (see functions.php)
			echo error(3.5);
    } elseif($try === false && $other === 'mac') {
      // Else returning see functions.php
      echo error(3.8, $customMessage);
		} else {
			// Else returning the third error (see functions.php)
			echo error(3);
		}
	}

	// Else if the content-type isn't JSON
	else
		// Returning the sixth error
		echo error(6);
}

// Else if the request method isn't POST
else
	// Returning the first error
	echo error(1);

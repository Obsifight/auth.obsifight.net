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

// If the request method is POST
if($request['method'] == "POST") {
		
		$input = file_get_contents("php://input");
		$getContents = json_decode($input, true);
		
		$accessToken = !empty($getContents['accessToken']) ? $getContents['accessToken'] : null;
		$playerUUID = !empty($getContents['selectedProfile']) ? $getContents['selectedProfile'] : null;
		$serverId = !empty($getContents['serverId']) ? $getContents['serverId'] : null;
		
		if(!is_null($accessToken) && !is_null($playerUUID) && !is_null($serverId)){
			$req = Core\Queries::execute('SELECT * FROM joueurs WHERE access_token=:accessToken', ['accessToken' => $accessToken]);
			
			if(empty($req))
				echo error(4);
			else {
				$name = $req->user_pseudo;

				$req = Core\Queries::execute('SELECT * FROM session_cache WHERE name=:name', ['name' => $name]);

				if(empty($req) || !is_object($req)){
					Core\Queries::execute('DELETE FROM session_cache WHERE name=:username', ['username' => $name]);
				}

				$req = Core\Queries::execute("INSERT INTO session_cache(name, profileID, serverID) VALUES(:name, :profileID, :serverID)",
				[
					'name' => $name,
					'profileID' => $playerUUID,
					'serverID' => $serverId
				]);
				
				$result = [
					'error' => "",
					'errorMessage' => "",
					'cause' => ""
				];
				$result = json_encode($result);
				echo $result;
			}
		}
		else
			echo error(4);
}
else
	echo error(1);

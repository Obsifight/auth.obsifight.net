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
if($request['method'] == "GET") {

		$serverId = !empty($_GET['serverId']) ? $_GET['serverId'] : null;
		$username = !empty($_GET['username']) ? $_GET['username'] : null;

		if(!is_null($username) && !is_null($serverId)){
			$req = Core\Queries::execute('SELECT * FROM session_cache WHERE name=:name', ['name' => $username]);

			if(empty($req) || !is_object($req))
				echo error(8);
			else {
				if ($req->serverID != $serverId) {
					Core\Queries::execute('DELETE FROM session_cache WHERE name=:username', ['username' => $username]);
					echo error(7);
				}
				else {
					$result = [
						'id' => $req->profileID,
						'properties' => [
							[
								'name' => "textures",
								'value' => "",
								'signature' => ""
							]
						]
					];
					$result = json_encode($result);
					Core\Queries::execute('DELETE FROM session_cache WHERE name=:username', ['username' => $username]);
					echo $result;
				}
			}
		}
		else
			echo error(4);
}
else
	echo error(1);

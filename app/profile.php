<?php

// If the request method is POST
if($request['method'] == "GET") {
	// If the content-type is JSON

	if(true){
		// Getting the sent content
		$UUID = $URLargs[1];
		$UUID = explode('?', $UUID)[0];

		$req = Core\Queries::execute("SELECT * FROM joueurs WHERE profileId = :UUID", ['UUID' => $UUID]);

		if(isset($req) && !empty($req)) {

      $user_has_skin = true;
      $user_has_cape = true;

			$textures = array();
      /*if($user_has_skin) {
        $textures['SKIN'] = 'http://skin.obsifight.fr/'.$UUID.'.png';
      }
      if($user_has_cape) {
        $textures['CAPE'] = 'http://cape.obsifight.fr/'.$UUID.'.png';
      }*/

      $textures['SKIN'] = 'http://51.255.48.29/skins/'.$req->user_pseudo.'.png';
			$textures['CAPE'] = 'http://51.255.48.29/capes/'.$req->user_pseudo.'_cape.png';

      echo json_encode(array(
        'id' => $UUID,
        'name' => $req->user_pseudo,
        'properties' => array(
          'name' => 'textures',
          'value' => base64_encode(json_encode(array(
            'timestamp' => time()*1000,
            'profileId' => $UUID,
            'profileName' => $req->user_pseudo,
            'isPublic' => true,
            'textures' => $textures
          )))
        )
      ));

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

?>

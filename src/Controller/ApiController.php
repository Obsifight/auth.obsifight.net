<?php
/*
* Copyright 2015 Vavaballz
*
* This file is part of OpenAuth-Server V2.
* OpenAuth-Server V2 is free software: you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* OpenAuth-Server V2 is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public License
* along with OpenAuth-Server V2.  If not, see <http://www.gnu.org/licenses/>.
*/
namespace App\Controller;

use App\Model\SessionCache;
use App\Model\User;
use App\Model\UsersConnectionLog;
use Slim\Http\Request;
use Slim\Http\Response;

class ApiController extends Controller
{

    private $salt = 'PApVSuS8hDUEsOEP0fWZESmODaHkXVst27CTnYMM';

    /**
     * @param Request $request
     * @param Response $response
     * @return array|Response|string
     */
    public function authenticate(Request $request, Response $response)
    {
        if (!onlyJsonRequest($request, $response))
            return null;
        $params = $request->getParams();

        $username = isset($params['username']) ? $params['username'] : null;
        $password = isset($params['password']) ? $params['password'] : null;
        $password = sha1($username . $this->salt . $password);
        $clientToken = isset($params['clientToken']) ? $params['clientToken'] : null;

        $user = User::where("username", $username)->where('password', $password)->first();
        if (!$user)
            return error(2, $response);

        // TODO: Check ObsiGuard

        // TODO: Check if user's mac is banned

        $log = new UsersConnectionLog();
        $log->user_id = $user->id;
        $log->type = 'LAUNCHER';
        $log->ip = $_SERVER['REMOTE_ADDR'];
        $log->save();

        $accessToken = md5(uniqid(rand(), true));
        if (is_null($clientToken))
            $clientToken = md5(uniqid(rand(), true));
        $user->access_token = $accessToken;
        $user->client_token = $clientToken;
        $user->save();

        return $response->withJson([
            'accessToken' => $accessToken,
            'clientToken' => $clientToken,
            'availableProfiles' => [
                [
                    'id' => $user->uuid,
                    'name' => $user->username
                ]
            ],
            'selectedProfile' => [
                'id' => $user->uuid,
                'name' => $user->username
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function refresh(Request $request, Response $response)
    {
        if (!onlyJsonRequest($request, $response))
            return null;
        $params = $request->getParams();

        $clientToken = !empty($params['clientToken']) ? $params['clientToken'] : null;
        $accessToken = !empty($params['accessToken']) ? $params['accessToken'] : null;

        $user = User::where("accessToken", $accessToken)->first();
        if (!$user)
            return error(3, $response);

        if ($user->clientToken != $clientToken)
            return error(2, $response);

        $user->accessToken = md5(uniqid(rand(), true));
        $user->save();

        return $response->withJson([
            'accessToken' => $user->accessToken,
            'clientToken' => $clientToken
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function validate(Request $request, Response $response){
        if (!onlyJsonRequest($request, $response))
            return null;
        $params = $request->getParams();

        $accessToken = !empty($params['accessToken']) ? $params['accessToken'] : null;

        if(is_null($accessToken))
            return error(3, $response);

        if(!User::where("access_token", $accessToken)->first())
            return error(3, $response);
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function signout(Request $request, Response $response){
        if (!onlyJsonRequest($request, $response))
            return null;
        $params = $request->getParams();

        $username = !empty($params['username']) ? $params['username'] : null;
        $password = !empty($params['password']) ? $params['password'] : null;

        if(!$username || !$password)
            return error(2, $response);

        if (!filter_var($username, FILTER_VALIDATE_EMAIL))
            return $response->withStatus(500)->withJson(['error' => 'Invalid Email', 'errorMessage' => 'The email field is not a valid email']);

        $user = User::where("username", $username)->first();

        if (!$user)
            return error(2, $response);

        if (!password_verify($password, $user->password))
            return error(2, $response);

        $user->access_token = null;
        $user->save();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return array|Response|string
     */
    public function invalidate(Request $request, Response $response){
        if (!onlyJsonRequest($request, $response))
            return null;
        $params = $request->getParams();

        $accessToken = !empty($params['accessToken']) ? $params['accessToken'] : null;
        $clientToken = !empty($params['clientToken']) ? $params['clientToken'] : null;

        if(empty($accessToken) || empty($clientToken))
            return error(3, $response);

        $user = User::where("access_token", $accessToken)->first();

        if(!$user)
            return error(3, $response);

        if ($clientToken != $user->client_token)
            return error(3, $response);

        $user->access_token = null;
        $user->save();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return array|Response|string
     */
    public function join(Request $request, Response $response){
        if (!onlyJsonRequest($request, $response))
            return null;
        $params = $request->getParams();

        $accessToken = !empty($params['accessToken']) ? $params['accessToken'] : null;
        $uuid = !empty($params['selectedProfile']) ? $params['selectedProfile'] : null;
        $serverId = !empty($params['serverId']) ? $params['serverId'] : null;

        if(empty($accessToken) || empty($uuid) || empty($serverId))
            return error(3, $response);

        $user = User::where("access_token", $accessToken)->first();
        if(!$user)
            return error(3, $response);

        SessionCache::where('uuid', $uuid)->delete();
        $cache = new SessionCache();
        $cache->username = $user->username;
        $cache->uuid = $uuid;
        $cache->server_id = $serverId;
        $cache->save();

        return $response->withJson([
            'error' => null,
            'errorMessage' => null,
            'cause' => null
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return array|Response|string
     */
    public function hasJoined(Request $request, Response $response){
        $params = $request->getQueryParams();

        $username = !empty($params['username']) ? $params['username'] : null;
        $serverId = !empty($params['serverId']) ? $params['serverId'] : null;

        if(empty($username) || empty($serverId))
            return error(3, $response);

        $cache = SessionCache::where('username', $username)->orderBy('id', 'desc')->first();
        if (!$cache)
            return error(7, $response);
        $uuid = $cache->uuid;
        SessionCache::where('username', $username)->delete();

        return $response->withJson([
            'id' => $uuid,
            'properties' => [
                [
                    'name' => "textures",
                    'value' => "",
                    'signature' => ""
                ]
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return array|Response|string
     */
    public function profile(Request $request, Response $response, $args){
        $params = $request->getQueryParams();

        if(!isset($args['uuid']))
            return error(3, $response);

        $uuid = $args['uuid'];
        $user = User::where('uuid', $uuid)->orderBy('id', 'desc')->first();
        if (!$user)
            return error(3, $response);

        return $response->withJson([
            'id' => $uuid,
            'name' => $user->username,
            'properties' => array(
                'name' => 'textures',
                'value' => base64_encode(json_encode([
                    'timestamp' => time() * 1000,
                    'profileId' => $uuid,
                    'profileName' => $user->username,
                    'isPublic' => true,
                    'textures' => [
                        'SKIN' => 'http://51.255.48.29/skins/' . $user->username . '.png',
                        'CAPE' => 'http://51.255.48.29/capes/' . $user->username . '_cape.png'
                    ]
                ]))
            )
        ]);
    }

    // TODO: Addresse MAC

}

<?php
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\User;

class UserController
{

    function userStat($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $classUser = new User($user_uuid);
        $res = $classUser->estadisticasGlobal();
        
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function userMvc($request, $response, $args)
    {
        $body = $request->getParsedBody();
        $classUser = new User($body['user_uuid']);
        $res = $classUser->verEstado();
        
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function updateToken($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $classUser = new User($user_uuid);
        $res = $classUser->generatedToken();
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}

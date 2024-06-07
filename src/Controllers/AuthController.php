<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Auth;

class AuthController
{

    function register($request, $response, $args)
    {

        $body = $request->getParsedBody();
        $classAuth = new Auth();
        $register = $classAuth->createAccount($body['nombre'], $body['apellido'], $body['correo'], $body['pass'], $body['ip']);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($register));
        return $response;
    }

    function login($request, $response, $args)
    {
        $body = $request->getParsedBody();
        $classAuth = new Auth();
        $login = $classAuth->logIn($body['username'], $body['pass']);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($login));
        return $response;
    }

    function logInGoogle ($request, $response, $args) {
        
    $body = $request->getParsedBody();
    $classAuth = new Auth();
    $login = $classAuth->loginWithGoogle($body['username'], $body['email'], $body['photo']);
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($login));
    return $response;
    }

    function updateStaticTokenFcm ($request, $response, $args) {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classAuth = new Auth();
        $login = $classAuth->verifyStaticTokenFcm($body["staticTokenFcm"], $user_uuid);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($login));
        return $response;
        }
}

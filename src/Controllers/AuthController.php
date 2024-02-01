<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Auth;
use App\Models\Notification;

class AuthController
{
    function register($request, $response, $args)
    {

        $body = $request->getParsedBody();
        $classAuth = new Auth();
        $register = $classAuth->createAccount($body['username'], $body['pass'], $body['ip']);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($register));
        return $response;
    }

    function registerN($request, $response, $args)
    {

        $body = $request->getParsedBody();
        $classAuth = new Auth();
        $register = $classAuth->createAccountN($body['nombre'], $body['apellido'], $body['correo'], $body['telefono'], $body['pass'], $body['ip']);
        $classNotification = new Notification();
        $bodyNoti = $body["nombre"] . " " . $body['apellido'] . " se ha registrado";
        $classNotification->crearNotificacion("👤 Nuevo Usuario", $bodyNoti);
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

    function validarEmail($request, $response, $args){
        $body = $request->getParsedBody();
        $classAuth = new Auth();
        $validar = $classAuth->emailStock($body['email']);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($validar));
        return $response;
    }

    function validarTelefono($request, $response, $args){
        $body = $request->getParsedBody();
        $classAuth = new Auth();
        $validar = $classAuth->telefonoStock($body['telefono']);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($validar));
        return $response;
    }
}

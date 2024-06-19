<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Email;
use App\Models\Notification;

class EmailController
{
    function listEmail($request, $response, $args)
    {
        $classEmail = new Email();
        $list = $classEmail->listEmails();
        $response->getBody()->write(json_encode($list));
        $response = $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        return $response;
    }

    function recoveryPassword($request, $response, $args)
    {
        $body = $request->getParsedBody();
        $classEmail = new Email();
        $instanciaNotificacion = new Notification();
        $list = $classEmail->recoveryPassword($body['email']);
        $cuerpoNotificacion = "Email de recuperacion enviado";
        $instanciaNotificacion->createNotification("Olvidaron Contraeña", $cuerpoNotificacion);
        $response->getBody()->write(json_encode($list));
        $response = $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        return $response;
    }
}

<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Notification;

class NotificationController
{
    private $res;

    function send($request, $response, $args)
    {
        $user_rol = $request->getAttribute('payload')->data->rol;

        if($user_rol == 'Admin'){
            $body = $request->getParsedBody();
            $tokens = $body["token_fcm"];
            $title = $body["title"];
            $message = $body["body"];
            $claseNotificacion = new Notification();
            $content = $claseNotificacion->createNotification([$tokens], $title, $message, null);
            $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($content));
            return $response;
        }else{
            $this->res['status'] = 'error';
            $this->res['message'] = 'No tienes el rango para el envio de notificaciones';
            $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($this->res));
            return $response;
        }
        
    }
}

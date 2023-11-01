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
            $title = '🏷️ ETIQUETA AGREGADA';
            $message = $body['username'] . ' agrego ' . $body['cantidad'] . ' etiquetas';
            $claseNotificacion = new Notification();
            $content = $claseNotificacion->crearNotificacion($title, $message);
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

    function sendGlobal($title, $message)
    {
        $claseNotificacion = new Notification();
        $content = $claseNotificacion->crearNotificacion($title, $message);
        
    }
}

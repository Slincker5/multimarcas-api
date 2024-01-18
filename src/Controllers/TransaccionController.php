<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Transaccion;

class TransaccionController {

    function savedTransaction($request, $response, $args){
        $body = $request->getParsedBody();
        $classTransaccion = new Transaccion();
        $save = $classTransaccion->savedTransaction($body['IdTransaccion'], $body['ResultadoTransaccion'], $body['Monto'], $body['FechaTransaccion']);
        $response->getBody()->write(json_encode($save));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}
<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Transaccion;

class TransaccionController
{

    function saveTransaction($request, $response, $args)
    {
        $body = $request->getParsedBody();
        $classTransaccion = new Transaccion();
        $save = $classTransaccion->saveTransaction($body['IdTransaccion'], $body['ResultadoTransaccion'], $body['Monto'], $body['FechaTransaccion']);
        $response->getBody()->write(json_encode($save));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function saveTransactionAfterPay($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classTransaccion = new Transaccion();
        $save = $classTransaccion->saveTransactionAfterPay($body["IdTransaccion"], $user_uuid);
        $response->getBody()->write(json_encode($save));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    }
}

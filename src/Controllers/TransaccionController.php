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
        $header_wompi = $request->getBody()->getContents();
        $wompiHashHeader = $request->getHeader('wompi_hash')[0];
        $classTransaccion = new Transaccion();
        $cliente = json_encode($body['cliente']);
        $save = $classTransaccion->saveTransaction($body['IdTransaccion'], $body['ResultadoTransaccion'], $body['Monto'], $body['FechaTransaccion'], $header_wompi, $wompiHashHeader, $cliente);
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

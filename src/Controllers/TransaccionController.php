<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Transaccion;
use App\Models\Notification;

class TransaccionController
{

    function saveTransaction($request, $response, $args)
    {
        $body = $request->getParsedBody();
        $cliente = $body['cliente'];
        $emailCliente = $cliente['Email'];
        $header_wompi = $request->getBody()->getContents();


        $bodyText = var_export($body, true);

        // Guardar $bodyText y $header_wompi en un archivo
        file_put_contents('transaction_data.txt', "Body: \n" . $bodyText . "\nHeader Wompi: \n" . $header_wompi . "\n", FILE_APPEND);

        $wompiHashHeader = $request->getHeader('wompi_hash')[0];
        $classTransaccion = new Transaccion();
        $save = $classTransaccion->saveTransaction($body['IdTransaccion'], $body['ResultadoTransaccion'], $body['Monto'], $body['FechaTransaccion'], $header_wompi, $wompiHashHeader);
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

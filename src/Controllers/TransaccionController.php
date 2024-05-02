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
        $header_wompi = $request->getBody()->getContents();
        $wompiHashHeader = $request->getHeader('wompi_hash')[0];

        $bodyJson = json_encode($body);
    // Especificar la ruta completa del archivo
    $filePath = '/home/admin/transaction_data.txt';
    // Guardar $bodyJson y $header_wompi en el archivo en la ruta especificada
    file_put_contents($filePath, "Body: " . $bodyJson . "\nHeader Wompi: " . $header_wompi . "\n", FILE_APPEND);


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

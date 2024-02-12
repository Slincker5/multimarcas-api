<?php
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Premiun;

class PremiunController
{

    function hacerPremiun($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $classPremiun = new Premiun($user_uuid);
        $vip = $classPremiun->hacerPremiun();
        $response->getBody()->write(json_encode($vip));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function generarCupon($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $classPremiun = new Premiun($user_uuid);
        $cupon = $classPremiun->generarCupon();
        $response->getBody()->write(json_encode($cupon));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function crearCupon($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classPremiun = new Premiun($user_uuid);
        $vip = $classPremiun->agregarCupon($body['limite_cupon'], $body['cupon']);
        $response->getBody()->write(json_encode($vip));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function canjearCupon($request, $response, $args){
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classPremiun = new Premiun($user_uuid);
        $cupon = $classPremiun->canjearCupon($body['cupon']);
        $response->getBody()->write(json_encode($cupon));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function estado($request, $response, $args){
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $classPremiun = new Premiun();
        $cupon = $classPremiun->validarSuscripcion($user_uuid);
        $response->getBody()->write(json_encode($cupon));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}

<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Poster;
use App\Models\Email;

class PosterController
{

    function createPoster($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classPoster = new Poster($body['barra'], $body['descripcion'], $body['precio'], $body['f_inicio'], $body['f_fin'], $body['cantidad'], $user_uuid);
        $create = $classPoster->createPoster();
        $response->getBody()->write(json_encode($create));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function listPoster($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classPoster = new Poster();
        $create = $classPoster->listPoster($user_uuid);
        $response->getBody()->write(json_encode($create));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function buildPosterDocument ($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $username = $request->getAttribute('payload')->data->username;
        $jwt = $request->getAttribute('jwt');
        $body = $request->getParsedBody();
        $classEmail = new Email();
        $classPoster = new Poster();
        $generar = shell_exec('python3 rotulos.py https://procter.work/api/poster/list ' . $jwt . ' ' . $user_uuid);
        $res = json_decode(trim($generar));
        $random_id = mt_rand(100000, 999999);
        $res->code = $random_id;

        if ($res->status === 'OK') {
            $guardarDocumento = $classPoster->saveGenerated($res->path_complete, $res->path_name, $res->path_uuid, $res->user_uuid, $body['comentarios'], $random_id, 'super_oferta_4x4');
            $asignarDocumento = $classPoster->assignDocument($res->path_uuid, $res->user_uuid);
        }

        if ($body !== null) {
            if (isset($res->path_complete)) {
                $asunto = 'AFICHES #' . $random_id;
                $regex = '/^[\p{L}\p{N}\s.,;:!?\'"áéíóúÁÉÍÓÚñÑ]+$/u';
                $comment = $body['comentarios'];
                if(!preg_match($regex, $comment)){
                    $comment = '---';
                }
                $correo = $classEmail->sendMailPoster($body['receptor'], $body['nombreReceptor'], $res->path_complete, $asunto, $comment, $res->cantidad, $username);
            }
        }
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    }

    function createPosterSmall($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classPoster = new Poster($body['barra'], $body['descripcion'], $body['precio'], $body['f_inicio'], $body['f_fin'], $body['cantidad'], $user_uuid);
        $create = $classPoster->createPosterSmall();
        $response->getBody()->write(json_encode($create));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function listPosterSmall($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classPoster = new Poster();
        $create = $classPoster->listPosterSmall($user_uuid);
        $response->getBody()->write(json_encode($create));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function listPosterLowPriceSmall($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classPoster = new Poster();
        $create = $classPoster->listPosterLowPriceSmall($user_uuid);
        $response->getBody()->write(json_encode($create));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function buildPosterDocumentSmall ($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $username = $request->getAttribute('payload')->data->username;
        $jwt = $request->getAttribute('jwt');
        $body = $request->getParsedBody();
        $classEmail = new Email();
        $classPoster = new Poster();
        $generar = shell_exec('python3 rotulos_mini.py https://procter.work/api/poster-small/list ' . $jwt . ' ' . $user_uuid);
        $res = json_decode(trim($generar));
        $random_id = mt_rand(100000, 999999);
        $res->code = $random_id;

        if ($res->status === 'OK') {
            $guardarDocumento = $classPoster->saveGenerated($res->path_complete, $res->path_name, $res->path_uuid, $res->user_uuid, $body['comentarios'], $random_id, 'super_oferta_3x9');
            $asignarDocumento = $classPoster->assignDocumentSmall($res->path_uuid, $res->user_uuid);
        }

        if ($body !== null) {
            if (isset($res->path_complete)) {
                $asunto = 'AFICHES #' . $random_id;
                $regex = '/^[\p{L}\p{N}\s.,;:!?\'"áéíóúÁÉÍÓÚñÑ]+$/u';
                $comment = $body['comentarios'];
                if(!preg_match($regex, $comment)){
                    $comment = '---';
                }
                $correo = $classEmail->sendMailPosterSmall($body['receptor'], $body['nombreReceptor'], $res->path_complete, $asunto, $comment, $res->cantidad, $username);
            }
        }
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    }
    
    function createPosterLowPriceSmall($request, $response, $args) {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classPoster = new Poster($body['barra'], $body['descripcion'], $body['precio'], null, null, $body['cantidad'], $user_uuid);
        $create = $classPoster->createPosterLowPriceSmall();
        $response->getBody()->write(json_encode($create));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}

    

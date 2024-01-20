<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Label;
use App\Models\Email;

class LabelController
{

    function createLabel($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $username = $request->getAttribute('payload')->data->username;
        $suscripcion = $request->getAttribute('payload')->data->suscripcion;
        $body = $request->getParsedBody();
        $classLabel = new Label($body['barra'], $body['descripcion'], $body['cantidad'], $body['precio'], $username, $user_uuid);
        $crear = $classLabel->addLabel($suscripcion);
        $response->getBody()->write(json_encode($crear));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function editLabel($request, $response, $args)
    {

        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classLabel = new Label();
        $content = $classLabel->editLabel($body, $user_uuid);
        $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($content));
        return $response;
    }

    function removeLabel($request, $response, $args)
    {

        $user_uuid = $request->getAttribute('payload')->data->user_uuid;

        $body = $request->getParsedBody();
        $classLabel = new Label();
        $content = $classLabel->eliminar($body['uuid'], $user_uuid);
        $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($content));
        return $response;
    }

    function listOfLabels($request, $response, $args)
    {

        $user_uuid = $request->getAttribute('payload')->data->user_uuid;

        $classLabel = new Label();
        $list = $classLabel->getLabels($user_uuid);
        $response->getBody()->write(json_encode($list));
        $response = $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        return $response;
    }

    function detailsLabels($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $uuid = $args['uuid'];
        $classLabel = new Label();
        $content = $classLabel->detailsLabels($user_uuid, $uuid);
        $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($content));
        return $response;
    }

    function build ($request, $response, $args){

        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $username = $request->getAttribute('payload')->data->username;
        $jwt = $request->getAttribute('jwt');
        $body = $request->getParsedBody();

        $classLabel = new Label();
        $classEmail = new Email();
        $generar = shell_exec("python3 cintillo.py https://procter.work/api/label/list $jwt $user_uuid");
        $res = json_decode(trim($generar));
        $random_id = mt_rand(100000, 999999);
        $res->code = $random_id;

        
        $ruta = escapeshellarg($res->path_complete);
        if ($res->status === 'OK') {
            $guardarDocumento = $classLabel->saveGenerated($res->path_complete, $res->path_name, $res->path_uuid, $res->user_uuid, $body['comentarios'], $random_id);
            $asignarDocumento = $classLabel->assignDocument($res->path_uuid, $res->user_uuid);
        }

        if ($body !== null) {
            if (isset($res->path_complete)) {
                $asunto = 'CINTILLOS #' . $random_id;
                $regex = '/^[\p{L}\p{N}\s.,;:!?\'"áéíóúÁÉÍÓÚñÑ]+$/u';
                $comment = $body['comentarios'];
                if(!preg_match($regex, $comment)){
                    $comment = '---';
                }
                $correo = $classEmail->sendMailLabel($body['receptor'], $body['nombreReceptor'], $res->path_complete, $asunto, $comment, $res->cantidad, $username);
            }
        }
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}

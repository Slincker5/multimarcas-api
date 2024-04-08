<?php

namespace App\Controllers;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Email;
use App\Models\Label;

class LabelController
{

    function createLabel($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $username = $request->getAttribute('payload')->data->username;
        $body = $request->getParsedBody();
        $classLabel = new Label($body['barra'], $body['descripcion'], $body['cantidad'], $body['precio'], $username, $user_uuid);
        $crear = $classLabel->addLabel();
        $response->getBody()->write(json_encode($crear));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function editLabel($request, $response, $args)
    {

        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classLabel = new Label(null, null, null, null, null, $user_uuid);
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

    function build($request, $response, $args)
    {

        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $username = $request->getAttribute('payload')->data->username;
        $jwt = $request->getAttribute('jwt');
        $body = $request->getParsedBody();

        $classLabel = new Label();
        $classEmail = new Email();
        $generar = shell_exec("python3 cintillo.py https://api.multimarcas.app/api/label/list $jwt $user_uuid");
        $res = json_decode(trim($generar));
        $random_id = mt_rand(100000, 999999);
        $res->code = $random_id;
        $partes = explode("@", $body["receptor"]);
        $nombreReceptor = $body["nombreReceptor"] === 'Desconocido' ? $partes[0] : $body['receptor'];
        if ($res->status === 'OK') {
            $classLabel->saveGenerated($res->path_complete, $res->path_name, $res->path_uuid, $res->user_uuid, $body['comentarios'], $random_id, $body['receptor'], $nombreReceptor);
            $classLabel->assignDocument($res->path_uuid, $res->user_uuid);
        }

        if ($body !== null) {
            if (isset($res->path_complete)) {
                $asunto = 'CINTILLOS #' . $random_id;
                $regex = '/^[\p{L}\p{N}\s.,;:!?\'"áéíóúÁÉÍÓÚñÑ]+$/u';
                $comment = $body['comentarios'];
                if (!preg_match($regex, $comment)) {
                    $comment = '---';
                }
                $classEmail->sendMailLabel($body['receptor'], $nombreReceptor, $res->path_complete, $asunto, $comment, $res->cantidad, $username);
            }
        }
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function listaGenerados($request, $response, $args)
    {

        $user_uuid = $request->getAttribute('payload')->data->user_uuid;

        $classLabel = new Label(null, null, null, null, null, $user_uuid);
        $list = $classLabel->listaGenerados($user_uuid);
        $response->getBody()->write(json_encode($list));
        $response = $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        return $response;
    }

    function documentGenerated($request, $response, $args)
    {

        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $path_uuid = $args['path_uuid'];
        $classLabel = new Label(null, null, null, null, null, $user_uuid);
        $list = $classLabel->getLabelGenerated($path_uuid);
        $response->getBody()->write(json_encode($list));
        $response = $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        return $response;
    }

    function resend($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $username = $request->getAttribute('payload')->data->username;
        $body = $request->getParsedBody();
        $classLabel = new Label(null, null, null, null, null, $user_uuid);
        if ($body !== null) {
            $res = $classLabel->resend($username, $body);
        }
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}

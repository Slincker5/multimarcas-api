<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\User;

class UserController
{

    function userStat($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $classUser = new User($user_uuid);
        $res = $classUser->estadisticasGlobal();

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function uploadPhoto($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['photo'];
        $fileType = $uploadedFile->getClientMediaType();
        $fileSize = $uploadedFile->getSize();
        $classUser = new User($user_uuid);
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $res = $classUser->uploadPhoto($uploadedFile, $fileType, $fileSize);
            $response->getBody()->write(json_encode($res));
            return $response;
        }
    }

    function updateToken($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $classUser = new User($user_uuid);
        $res = $classUser->generatedToken();
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function updateTokenNotificacion($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classUser = new User($user_uuid);
        $res = $classUser->updateTokenNotification($body["token_fcm"]);
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function topGlobal($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $classUser = new User($user_uuid);
        $res = $classUser->getTopAll();
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function editProfile($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classUser = new User($user_uuid);
        $res = $classUser->editProfile($body["nombre"], $body["apellido"], $body["telefono"]);
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function editPasswordProfile($request, $response, $args)
    {

        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $body = $request->getParsedBody();
        $classUser = new User($user_uuid);
        $res = $classUser->editPasswordProfile($body["passwordNow"], $body["password"], $body["newPassword"]);
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function editPasswordRecovery($request, $response, $args)
    {
        $body = $request->getParsedBody();
        $classUser = new User();
        $res = $classUser->editPasswordRecovery($body["email"], $body["password"], $body["newPassword"]);
        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}

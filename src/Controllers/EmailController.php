<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Email;

class EmailController
{
    function listEmail($request, $response, $args)
    {
        $classEmail = new Email();
        $list = $classEmail->listEmails();
        $response->getBody()->write(json_encode($list));
        $response = $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        return $response;
    }

    function recoveryPassword($request, $response, $args)
    {
        $body = $request->getParsedBody();
        $classEmail = new Email();
        $list = $classEmail->recoveryPassword($body['email']);
        $response->getBody()->write(json_encode($list));
        $response = $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        return $response;
    }
    function validateCodeEmail($request, $response, $args)
    {
        $body = $request->getParsedBody();
        $classEmail = new Email();
        $list = $classEmail->validateCodeEmail($body['code']);
        $response->getBody()->write(json_encode($list));
        $response = $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        return $response;
    }
}

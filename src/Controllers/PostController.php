<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Post;

class PostController
{

    function newPost($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $username = $request->getAttribute('payload')->data->username;
        $body = $request->getParsedBody();
        $classPost = new Post($user_uuid, $username);
        $createPost = $classPost->newPost($body["message"]);
        $response->getBody()->write(json_encode($createPost));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}

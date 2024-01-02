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
        $photo = $request->getAttribute('payload')->data->photo;
        $body = $request->getParsedBody();
        $classPost = new Post($user_uuid, $username);
        $createPost = $classPost->newPost($body["message"], $photo);
        $response->getBody()->write(json_encode($createPost));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function listPost($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $username = $request->getAttribute('payload')->data->username;
        $classPost = new Post($user_uuid, $username);
        $listPost = $classPost->listPost();
        $response->getBody()->write(json_encode($listPost));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function removePost($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $classPost = new Post($user_uuid, $username);
        $removePost = $classPost->removePost($user_uuid);
        $response->getBody()->write(json_encode($removePost));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    }
}

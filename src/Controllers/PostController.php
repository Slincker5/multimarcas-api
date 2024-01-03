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
        $username = $request->getAttribute('payload')->data->username;
        $body = $request->getParsedBody();
        $classPost = new Post($user_uuid, $username);
        $removePost = $classPost->removePost($body["post_uuid"]);
        $response->getBody()->write(json_encode($removePost));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    }

    function likePost($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $username = $request->getAttribute('payload')->data->username;
        $body = $request->getParsedBody();
        $classPost = new Post($user_uuid, $username);
        $likePost = $classPost->likePost($body["post_uuid"]);
        $response->getBody()->write(json_encode($likePost));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    }

    function listLikePost($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $username = $request->getAttribute('payload')->data->username;
        $body = $request->getParsedBody();
        $classPost = new Post($user_uuid, $username);
        $listLikePost = $classPost->listLikePost();
        $response->getBody()->write(json_encode($listLikePost));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    function selectPost($request, $response, $args)
    {
        $user_uuid = $request->getAttribute('payload')->data->user_uuid;
        $username = $request->getAttribute('payload')->data->username;
        $body = $request->getParsedBody();
        $classPost = new Post($user_uuid, $username);
        $selectPost = $classPost->selectPost();
        $response->getBody()->write(json_encode($selectPost));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}

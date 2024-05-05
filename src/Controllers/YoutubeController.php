<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Youtube;

class YoutubeController {

    function search($request, $response, $args){

        $text = $args['search'];
        $classSearch = new Youtube();
        $results = $classSearch->searchYouTube($text);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($results));
        return $response;
    }
}
<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Models\Search;

class SearchController {

    function search($request, $response, $args){

        $text = $args['article'];
        $classSearch = new Search();
        $results = $classSearch->searching($text);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($results));
        return $response;
    }
}
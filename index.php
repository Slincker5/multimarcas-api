<?php

use App\Controllers\AuthController;
use App\Controllers\LabelController;
use App\Controllers\PosterController;
use App\Controllers\EmailController;
use App\Controllers\UserController;
use App\Controllers\SearchController;
use App\Controllers\PostController;
use App\Controllers\PremiunController;
use App\Controllers\TransaccionController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;

# My class
use Slim\Psr7\Response;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->setBasePath('/api');
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$errorMiddleware->setErrorHandler(
    HttpNotFoundException::class,
    function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {
        $response = new Response();
        $response->getBody()->write('404 - Recurso no encontrado.');

        return $response->withStatus(404);
    });

$validateJwtMiddleware = function ($request, $handler) {
    $response = new Response();
    $key = "georginalissethyvladi";
    $authHeader = $request->getHeaderLine('Authorization');
    if (!$authHeader) {
        $response->getBody()->write(json_encode(["error" => "Token no proporcionado"]));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }

    #EXTRAER TOKEN DE LA CABEZERA
    list($jwt) = sscanf($authHeader, 'Bearer %s');

    #VALIDAR SI LA CABEZERA CONTIENE ALGUN TOKEN
    if (!$jwt) {
        $response->getBody()->write(json_encode(["error" => "Token no encontrado en la cabecera Authorization"]));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }

    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        // Aquí puedes incluso agregar el payload decodificado al request si lo necesitas después
        $request = $request->withAttribute('payload', $decoded);
        $request = $request->withAttribute('jwt', $jwt);
    } catch (Exception $e) {
        $response = new Response();
        $response->getBody()->write('Token no válido: ' . $e->getMessage());
        return $response->withStatus(401); // Unauthorized
    }

    return $handler->handle($request);
};

$app->group('/auth', function ($group) {

    $group->post('/register', AuthController::class . ':register');
    $group->post('/login', AuthController::class . ':login');
    $group->post('/login-with-google', AuthController::class . ':logInGoogle');

});

$app->group('/label', function ($group) {

    $group->post('/create', LabelController::class . ':createLabel');
    $group->put('/edit', LabelController::class . ':editLabel');
    $group->delete('/remove', LabelController::class . ':removeLabel');
    $group->get('/list', LabelController::class . ':listOfLabels');
    $group->get('/details/{uuid}', LabelController::class . ':detailsLabels');
    $group->post('/build-document', LabelController::class . ':build');

})->add($validateJwtMiddleware);

$app->group('/notification', function ($group) {

    $group->post('/create', NotificationController::class . ':send');

})->add($validateJwtMiddleware);

$app->group('/search', function($group) {

    $group->get('/all/{article}', SearchController::class . ':search');
    
})->add($validateJwtMiddleware);


$app->group('/poster', function ($group) {

    $group->post('/create', PosterController::class . ':createPoster');
    $group->get('/list', PosterController::class . ':listPoster');
    $group->post('/build', PosterController::class . ':buildPosterDocument');

})->add($validateJwtMiddleware);

$app->group('/poster-small', function ($group) {

    $group->post('/create', PosterController::class . ':createPosterSmall');
    $group->get('/list', PosterController::class . ':listPosterSmall');
    $group->delete('/remove', PosterController::class . ':removePosterSmall');
    $group->post('/build', PosterController::class . ':buildPosterDocumentSmall');

})->add($validateJwtMiddleware);

$app->group('/poster-low-price-small', function ($group) {

    $group->post('/create', PosterController::class . ':createPosterLowPriceSmall');
    $group->get('/list', PosterController::class . ':listPosterLowPriceSmall');
    $group->post('/build', PosterController::class . ':buildPosterLowPriceDocumentSmall');

})->add($validateJwtMiddleware);

$app->group('/email', function ($group) {

    $group->get('/list', EmailController::class . ':listEmail');

})->add($validateJwtMiddleware);

$app->group('/user', function ($group) {

    $group->get('/stats', UserController::class . ':userStat');

})->add($validateJwtMiddleware);

$app->group('/post', function ($group) {

    $group->post('/create', PostController::class . ':newPost');
    $group->get('/list', PostController::class . ':listPost');
    $group->delete('/remove', PostController::class . ':removePost');
    $group->post('/like', PostController::class . ':likePost');
    $group->get('/list-like', PostController::class . ':listLikePost');
    $group->get('/select-post/{post_uuid}', PostController::class . ':selectPost');

})->add($validateJwtMiddleware);

$app->group('/comment', function ($group) {

    $group->post('/create', PostController::class . ':newComment');
    $group->get('/list/{post_uuid}', PostController::class . ':listComment');

})->add($validateJwtMiddleware);

$app->group('/temp', function ($group) {
    
    $group->post('/mvc', UserController::class . ':userMvc');
    $group->post('/update-token', UserController::class . ':updateToken');
    
})->add($validateJwtMiddleware);

$app->group('/premiun', function ($group) {
    $group->get('/cupon', PremiunController::class . ':generarCupon');
    $group->post('/nuevo-cupon', PremiunController::class . ':crearCupon');
    $group->post('/canjear-cupon', PremiunController::class . ':canjearCupon');
})->add($validateJwtMiddleware);

$app->group('/pagos', function ($group) {
    $group->post('/webhook', TransaccionController::class . ':saveTransaction');
    $group->post('/after-pay', TransaccionController::class . ':saveTransactionAfterPay');
});
$app->run();


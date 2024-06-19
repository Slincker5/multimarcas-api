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
use App\Controllers\LinkController;
use App\Controllers\CounterController;
use App\Controllers\NotificationController;
use App\Controllers\YoutubeController;
use App\Models\Auth;
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
        $paquete = [
            "status" => "invalid",
            "message" => "Token no valido."
        ];
        $response->getBody()->write(json_encode($paquete));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
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
    $group->get('/list-generated', LabelController::class . ':listaGenerados');
    $group->get('/details/{uuid}', LabelController::class . ':detailsLabels');
    $group->post('/build-document', LabelController::class . ':build');
    $group->post('/resend', LabelController::class . ':resend');

})->add($validateJwtMiddleware);


$app->group('/search', function($group) {

    $group->get('/all/{article}', SearchController::class . ':search');
    
})->add($validateJwtMiddleware);

$app->group('/youtube', function($group) {

    $group->get('/search/{search}', YoutubeController::class . ':search');
    $group->post('/download', YoutubeController::class . ':download');
    
});

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

$app->group('/poster-small-desc', function ($group) {

    $group->post('/create', PosterController::class . ':createPosterSmallDesc');
    $group->get('/list', PosterController::class . ':listPosterSmallDesc');
    $group->delete('/remove', PosterController::class . ':removePosterSmallDesc');
    $group->post('/build', PosterController::class . ':buildPosterDocumentSmallDesc');

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
    $group->get('/top-global', UserController::class . ':topGlobal');

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
    $group->post('/update-token', UserController::class . ':updateToken');
    
})->add($validateJwtMiddleware);

$app->group('/premiun', function ($group) {
    $group->get('/cupon', PremiunController::class . ':generarCupon');
    $group->post('/nuevo-cupon', PremiunController::class . ':crearCupon');
    $group->post('/canjear-cupon', PremiunController::class . ':canjearCupon');
})->add($validateJwtMiddleware);

$app->group('/pagos', function ($group) {
    $group->post('/webhook', TransaccionController::class . ':saveTransaction');
});

$app->group('/pagos', function ($group) {
    $group->post('/after-pay', TransaccionController::class . ':saveTransactionAfterPay');
})->add($validateJwtMiddleware);

$app->group('/notification', function ($group) {
    $group->post('/create', NotificationController::class . ':send');
    $group->post('/global-create', NotificationController::class . ':sendGlobal');
    $group->post('/premium-create', NotificationController::class . ':sendGlobalPremiumEnd');
})->add($validateJwtMiddleware);




// APLICACION DE JOSUE GABRIEL

$app->group('/link', function ($group) {

    $group->post('/create', LinkController::class . ':create');
    $group->get('/list', LinkController::class . ':list');
    $group->put('/edit', LinkController::class . ':edit');
    $group->post('/remove', LinkController::class . ':remove');
    $group->get('/clics', LinkController::class . ':clics');
    $group->post('/view', LinkController::class . ':view');
    $group->post('/viewcountry', LinkController::class . ':viewcountry');


})->add($validateJwtMiddleware);

$app->group('/view', function ($group) {

    $group->post('/logger', CounterController::class . ':view');
    $group->post('/validate', CounterController::class . ':validate');

});


$app->group('/user', function ($group) {

    $group->post('/upload-photo', UserController::class . ':uploadPhoto');
    $group->post('/edit-profile', UserController::class . ':editProfile');
    $group->post('/edit-password', UserController::class . ':editPasswordProfile');
    $group->post('/reload-token-fcm', UserController::class . ':updateTokenNotificacion');
    $group->post('/verify-statict-token', AuthController::class . ':updateStaticTokenFcm');

})->add($validateJwtMiddleware);

$app->group('/recovery', function ($group) {

    $group->post('/password', EmailController::class . ':recoveryPassword');

});

$app->group('/detail', function($group) {

    $group->get('/label/{path_uuid}', LabelController::class . ':documentGenerated');
    
})->add($validateJwtMiddleware);

$app->run();


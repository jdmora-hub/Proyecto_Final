<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/global/database_config.php';

$cors = require __DIR__ . '/../app/global/cors_config.php';
$auth_routers = require __DIR__ . '/../app/Presentation/Routers/endpoints.php';

$app = AppFactory::create();

$cors($app);
$auth_routers($app);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write(json_encode(['servicio' => 'ms-auth activo']));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
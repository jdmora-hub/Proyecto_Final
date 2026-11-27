<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/global/database_config.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$cors = require __DIR__ . '/../app/global/cors_config.php';
$cors($app);

$routes = require __DIR__ . '/../app/Presentation/Routers/endpoints.php';
$routes($app);

$app->run();
<?php

declare(strict_types=1);

use endpoints\EndpointRegistrator;
use config\Bootstrap;
use middlewares\AuthenticationMiddleware;
use WebApiCore\Builder\AppBuilder;

require __DIR__ . "/src/vendor/autoload.php";

set_error_handler("config\ErrorHandler::handleError");
set_exception_handler("config\ErrorHandler::handleException");

$builder = AppBuilder::createBuilder();

Bootstrap::bootstrapApp($builder->Container, $builder->Configuration);

$app = $builder->buildApp();

$endpoints = $app->getInstance(EndpointRegistrator::class);

$app->addRouter($endpoints->registerEndpoints());
$app->useMiddlewareOfType(AuthenticationMiddleware::class);

$app->process();

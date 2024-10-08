<?php

declare(strict_types=1);

use endpoints\EndpointRegistrator;
use config\Bootstrap;
use middlewares\AuthenticationMiddleware;
use WebApiCore\Container\AppBuilder;

require __DIR__ . "/src/vendor/autoload.php";

set_error_handler("config\ErrorHandler::handleError");
set_exception_handler("config\ErrorHandler::handleException");

$builder = AppBuilder::createBuilder();
$builder = Bootstrap::addDatabase($builder);
$builder = Bootstrap::addServices($builder);
$builder = Bootstrap::addRepositories($builder);

$app = $builder->buildApp();

$endpoints = $app->getInstance(EndpointRegistrator::class);

$app->addRouter($endpoints->registerEndpoints());
$app->useMiddlewareOfType(AuthenticationMiddleware::class);

$app->process();

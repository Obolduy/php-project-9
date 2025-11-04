<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Настройка Twig
$twig = Twig::create(__DIR__ . '/../resources/views', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

$app->get('/', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'index.twig');
});

$app->run();
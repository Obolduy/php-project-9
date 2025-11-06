<?php

use DI\Container;
use Dotenv\Dotenv;
use Hexlet\Code\Connection;
use Hexlet\Code\Controllers\HomeController;
use Hexlet\Code\Controllers\UrlController;
use Hexlet\Code\Repositories\UrlRepository;
use Hexlet\Code\Repositories\UrlAnalysisRepository;
use Hexlet\Code\Services\FlashService;
use Hexlet\Code\Services\UrlNormalizer;
use Hexlet\Code\Services\UrlCheckerService;
use Hexlet\Code\Twig\RoutesExtension;
use Hexlet\Code\Validators\UrlValidator;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$container = new Container();

$container->set(FlashService::class, function () {
    return new FlashService();
});

$container->set(UrlNormalizer::class, function () {
    return new UrlNormalizer();
});

$container->set(UrlValidator::class, function (ContainerInterface $c) {
    return new UrlValidator($c->get(UrlNormalizer::class));
});

$container->set(UrlRepository::class, function () {
    $pdo = Connection::get();
    return new UrlRepository($pdo);
});

$container->set(UrlAnalysisRepository::class, function () {
    $pdo = Connection::get();
    return new UrlAnalysisRepository($pdo);
});

$container->set(UrlCheckerService::class, function () {
    return new UrlCheckerService();
});

$container->set(HomeController::class, function (ContainerInterface $c) {
    return new HomeController($c->get(FlashService::class));
});

$container->set(UrlController::class, function (ContainerInterface $c) {
    return new UrlController(
        $c->get(UrlRepository::class),
        $c->get(UrlAnalysisRepository::class),
        $c->get(UrlValidator::class),
        $c->get(UrlNormalizer::class),
        $c->get(FlashService::class),
        $c->get(UrlCheckerService::class)
    );
});

AppFactory::setContainer($container);

$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/../resources/views', ['cache' => false]);
$twig->getEnvironment()->addExtension(new RoutesExtension());

$app->add(TwigMiddleware::create($app, $twig));

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true);

return $app;

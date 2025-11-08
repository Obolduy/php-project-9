<?php

use Hexlet\Code\Common\Controllers\HomeController;
use Hexlet\Code\Config\Routes;
use Hexlet\Code\Url\Controllers\UrlController;

$app = require __DIR__ . '/../app/bootstrap.php';

$app->get('/', [HomeController::class, 'index'])->setName(Routes::HOME);

$app->get('/urls', [UrlController::class, 'index'])->setName(Routes::URLS_INDEX);
$app->post('/urls', [UrlController::class, 'store'])->setName(Routes::URLS_STORE);
$app->get('/urls/{id}', [UrlController::class, 'show'])->setName(Routes::URLS_SHOW);
$app->post('/urls/{id}/checks', [UrlController::class, 'createCheck'])->setName(Routes::URLS_CHECKS_STORE);

$app->run();
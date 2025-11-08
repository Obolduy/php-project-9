<?php

namespace Hexlet\Code\Common\Controllers;

use Hexlet\Code\Common\Services\FlashService;
use Hexlet\Code\Config\Routes;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController
{
    private FlashService $flash;

    public function __construct(FlashService $flash)
    {
        $this->flash = $flash;
    }

    public function index(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);

        $params = [
            'flash' => $this->flash->getAll(),
            'errors' => [],
            'url' => [],
            'currentRoute' => Routes::HOME
        ];

        return $view->render($response, 'index.twig', $params);
    }
}

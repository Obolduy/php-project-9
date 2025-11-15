<?php

namespace Hexlet\Code\Common\Controllers;

use Hexlet\Code\Config\Routes;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig;

class HomeController
{
    private FlashMessages $flash;

    public function __construct(FlashMessages $flash)
    {
        $this->flash = $flash;
    }

    public function index(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);

        $params = [
            'flash' => $this->flash->getMessages(),
            'errors' => [],
            'url' => [],
            'currentRoute' => Routes::HOME
        ];

        return $view->render($response, 'urls/index.twig', $params);
    }
}

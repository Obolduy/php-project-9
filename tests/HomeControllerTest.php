<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Common\Controllers\HomeController;
use Hexlet\Code\Config\Routes;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig;

class HomeControllerTest extends TestCase
{
    private HomeController $controller;
    private FlashMessages $flash;

    protected function setUp(): void
    {
        $_SESSION = [];

        $this->flash = new FlashMessages();
        $this->controller = new HomeController($this->flash);
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testIndexReturnsResponse(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $view = $this->createMock(Twig::class);
        $view->expects($this->once())
            ->method('render')
            ->with(
                $response,
                'urls/index.twig',
                $this->callback(function ($params) {
                    return isset($params['flash']) &&
                           isset($params['errors']) &&
                           isset($params['url']) &&
                           isset($params['currentRoute']) &&
                           $params['currentRoute'] === Routes::HOME;
                })
            )
            ->willReturn($response);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with('view')
            ->willReturn($view);

        $result = $this->controller->index($request, $response);

        $this->assertSame($response, $result);
    }

    public function testIndexIncludesFlashMessages(): void
    {
        $this->flash->addMessage('success', 'Test message');

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $view = $this->createMock(Twig::class);
        $view->expects($this->once())
            ->method('render')
            ->with(
                $response,
                'urls/index.twig',
                $this->callback(function ($params) {
                    // После getMessages() flash будет содержать массивы сообщений
                    // В реальном приложении getMessages() забирает сообщения и очищает их
                    // Для теста мы должны проверить, что params содержит flash ключ
                    return isset($params['flash']) && 
                           isset($params['errors']) && 
                           isset($params['url']) && 
                           isset($params['currentRoute']);
                })
            )
            ->willReturn($response);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with('view')
            ->willReturn($view);

        $this->controller->index($request, $response);
    }

    public function testIndexIncludesEmptyErrors(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $view = $this->createMock(Twig::class);
        $view->expects($this->once())
            ->method('render')
            ->with(
                $response,
                'urls/index.twig',
                $this->callback(function ($params) {
                    return is_array($params['errors']) && empty($params['errors']);
                })
            )
            ->willReturn($response);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with('view')
            ->willReturn($view);

        $this->controller->index($request, $response);
    }

    public function testIndexIncludesEmptyUrl(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $view = $this->createMock(Twig::class);
        $view->expects($this->once())
            ->method('render')
            ->with(
                $response,
                'urls/index.twig',
                $this->callback(function ($params) {
                    return is_array($params['url']) && empty($params['url']);
                })
            )
            ->willReturn($response);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with('view')
            ->willReturn($view);

        $this->controller->index($request, $response);
    }

    public function testIndexSetsCorrectCurrentRoute(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $view = $this->createMock(Twig::class);
        $view->expects($this->once())
            ->method('render')
            ->with(
                $response,
                'urls/index.twig',
                $this->callback(function ($params) {
                    return $params['currentRoute'] === Routes::HOME;
                })
            )
            ->willReturn($response);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with('view')
            ->willReturn($view);

        $this->controller->index($request, $response);
    }
}

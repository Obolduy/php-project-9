<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Common\Controllers\HomeController;
use Hexlet\Code\Common\Services\FlashService;
use Hexlet\Code\Config\Routes;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class HomeControllerTest extends TestCase
{
    private HomeController $controller;
    private FlashService $flash;

    protected function setUp(): void
    {
        $_SESSION = [];

        $this->flash = new FlashService();
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
                'index.twig',
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
        $this->flash->set('success', 'Test message');

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $view = $this->createMock(Twig::class);
        $view->expects($this->once())
            ->method('render')
            ->with(
                $response,
                'index.twig',
                $this->callback(function ($params) {
                    return isset($params['flash']['success']) &&
                           $params['flash']['success'] === 'Test message';
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
                'index.twig',
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
                'index.twig',
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
                'index.twig',
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

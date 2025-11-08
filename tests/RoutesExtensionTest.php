<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Config\Routes;
use Hexlet\Code\Twig\RoutesExtension;
use PHPUnit\Framework\TestCase;

class RoutesExtensionTest extends TestCase
{
    private RoutesExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new RoutesExtension();
    }

    public function testGetGlobalsReturnsArray(): void
    {
        $globals = $this->extension->getGlobals();

        $this->assertIsArray($globals);
        $this->assertArrayHasKey('routes', $globals);
    }

    public function testGetGlobalsContainsAllRoutes(): void
    {
        $globals = $this->extension->getGlobals();
        $routes = $globals['routes'];

        $this->assertArrayHasKey('home', $routes);
        $this->assertArrayHasKey('urls_store', $routes);
        $this->assertArrayHasKey('urls_show', $routes);
        $this->assertArrayHasKey('urls_index', $routes);
        $this->assertArrayHasKey('urls_checks_store', $routes);
    }

    public function testGetGlobalsReturnCorrectRouteValues(): void
    {
        $globals = $this->extension->getGlobals();
        $routes = $globals['routes'];

        $this->assertEquals(Routes::HOME, $routes['home']);
        $this->assertEquals(Routes::URLS_STORE, $routes['urls_store']);
        $this->assertEquals(Routes::URLS_SHOW, $routes['urls_show']);
        $this->assertEquals(Routes::URLS_INDEX, $routes['urls_index']);
        $this->assertEquals(Routes::URLS_CHECKS_STORE, $routes['urls_checks_store']);
    }
}

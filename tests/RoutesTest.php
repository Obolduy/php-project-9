<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Config\Routes;
use PHPUnit\Framework\TestCase;

class RoutesTest extends TestCase
{
    public function testAllRoutesAreStrings(): void
    {
        $this->assertIsString(Routes::HOME);
        $this->assertIsString(Routes::URLS_STORE);
        $this->assertIsString(Routes::URLS_SHOW);
        $this->assertIsString(Routes::URLS_INDEX);
        $this->assertIsString(Routes::URLS_CHECKS_STORE);
    }

    public function testAllRoutesAreNotEmpty(): void
    {
        $this->assertNotEmpty(Routes::HOME);
        $this->assertNotEmpty(Routes::URLS_STORE);
        $this->assertNotEmpty(Routes::URLS_SHOW);
        $this->assertNotEmpty(Routes::URLS_INDEX);
        $this->assertNotEmpty(Routes::URLS_CHECKS_STORE);
    }

    public function testHomeRouteValue(): void
    {
        $this->assertEquals('home', Routes::HOME);
    }

    public function testUrlsStoreRouteValue(): void
    {
        $this->assertEquals('urls.store', Routes::URLS_STORE);
    }

    public function testUrlsShowRouteValue(): void
    {
        $this->assertEquals('urls.show', Routes::URLS_SHOW);
    }

    public function testUrlsIndexRouteValue(): void
    {
        $this->assertEquals('urls.index', Routes::URLS_INDEX);
    }

    public function testUrlsChecksStoreRouteValue(): void
    {
        $this->assertEquals('urls.checks.store', Routes::URLS_CHECKS_STORE);
    }

    public function testAllRoutesAreUnique(): void
    {
        $routes = [
            Routes::HOME,
            Routes::URLS_STORE,
            Routes::URLS_SHOW,
            Routes::URLS_INDEX,
            Routes::URLS_CHECKS_STORE,
        ];

        $uniqueRoutes = array_unique($routes);

        $this->assertCount(count($routes), $uniqueRoutes);
    }
}

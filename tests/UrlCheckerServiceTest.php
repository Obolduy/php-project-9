<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Services\UrlCheckerService;
use PHPUnit\Framework\TestCase;

class UrlCheckerServiceTest extends TestCase
{
    private UrlCheckerService $checker;

    protected function setUp(): void
    {
        $this->checker = new UrlCheckerService();
    }

    public function testCheckReturnsResponseCodeForValidUrl(): void
    {
        $result = $this->checker->check('https://www.example.com');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('response_code', $result);
        $this->assertIsInt($result['response_code']);
        $this->assertGreaterThanOrEqual(200, $result['response_code']);
        $this->assertLessThan(600, $result['response_code']);
    }

    public function testCheckReturnsNullForInvalidUrl(): void
    {
        $result = $this->checker->check('http://этотсайтнесуществует123456789.com');

        $this->assertNull($result);
    }

    public function testCheckExtractsHtmlElements(): void
    {
        $result = $this->checker->check('https://www.example.com');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('h1', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);

        $this->assertIsString($result['title']);
        $this->assertNotEmpty($result['title']);
        $this->assertStringContainsString('Example', $result['title']);
    }

    public function testCheckExtractsH1WhenPresent(): void
    {
        $result = $this->checker->check('https://www.example.com');

        $this->assertIsArray($result);

        if ($result['h1'] !== null) {
            $this->assertIsString($result['h1']);
            $this->assertNotEmpty($result['h1']);
        }
    }
}

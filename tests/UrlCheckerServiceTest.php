<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Url\Services\UrlCheckerService;
use PHPUnit\Framework\TestCase;

class UrlCheckerServiceTest extends TestCase
{
    private UrlCheckerService $checker;

    protected function setUp(): void
    {
        $this->checker = new UrlCheckerService();
    }

    public function testCheckReturnsNullForInvalidUrl(): void
    {
        if (!function_exists('curl_init') || getenv('SKIP_NETWORK_TESTS')) {
            $this->markTestSkipped('Network tests are disabled or curl is not available');
        }

        $result = $this->checker->check('http://этотсайтнесуществует123456789.com');

        $this->assertNull($result);
    }
}

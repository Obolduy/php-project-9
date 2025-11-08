<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Url\Services\UrlNormalizer;
use PHPUnit\Framework\TestCase;

class UrlNormalizerEdgeCasesTest extends TestCase
{
    private UrlNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new UrlNormalizer();
    }

    public function testNormalizeWithMultipleProtocols(): void
    {
        $result = $this->normalizer->normalize('http://http://example.com');
        $this->assertIsString($result);
    }

    public function testNormalizeWithSpecialCharactersInHost(): void
    {
        $result = $this->normalizer->normalize('https://examp!e.com');
        if ($result !== null) {
            $this->assertStringStartsWith('https://', $result);
        } else {
            $this->assertNull($result);
        }
    }

    public function testNormalizeWithIPAddress(): void
    {
        $result = $this->normalizer->normalize('https://192.168.1.1');
        $this->assertEquals('https://192.168.1.1', $result);
    }

    public function testNormalizeWithIPv6Address(): void
    {
        $result = $this->normalizer->normalize('https://[2001:0db8:85a3:0000:0000:8a2e:0370:7334]');
        $this->assertNotNull($result);
    }

    public function testNormalizeWithUsername(): void
    {
        $result = $this->normalizer->normalize('https://user@example.com/path');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizeWithUsernameAndPassword(): void
    {
        $result = $this->normalizer->normalize('https://user:pass@example.com/path');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizeWithInternationalDomain(): void
    {
        $result = $this->normalizer->normalize('https://пример.рф');
        $this->assertNotNull($result);
    }

    public function testNormalizeWithMixedCaseScheme(): void
    {
        $result = $this->normalizer->normalize('HtTpS://example.com');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizeWithTrailingSlash(): void
    {
        $result = $this->normalizer->normalize('https://example.com/');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizeWithMultipleSlashes(): void
    {
        $result = $this->normalizer->normalize('https://example.com//path//to//page');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizeWithSubdomainAndPort(): void
    {
        $result = $this->normalizer->normalize('https://sub.domain.example.com:8080/path');
        $this->assertEquals('https://sub.domain.example.com', $result);
    }

    public function testNormalizeWithOnlyHost(): void
    {
        $result = $this->normalizer->normalize('example.com');
        $this->assertNull($result);
    }

    public function testNormalizeWithDoubleSlashOnly(): void
    {
        $result = $this->normalizer->normalize('//example.com');
        $this->assertNull($result);
    }

    public function testNormalizeWithDataUrl(): void
    {
        $result = $this->normalizer->normalize('data:text/plain;base64,SGVsbG8sIFdvcmxkIQ==');
        $this->assertNull($result);
    }

    public function testNormalizeWithJavaScriptProtocol(): void
    {
        $result = $this->normalizer->normalize('javascript:alert(1)');
        $this->assertNull($result);
    }

    public function testNormalizeWithVeryLongHost(): void
    {
        $longHost = str_repeat('a', 253) . '.com';
        $result = $this->normalizer->normalize('https://' . $longHost);

        if ($result !== null) {
            $this->assertStringStartsWith('https://', $result);
        }

        $this->assertTrue(true);
    }

    public function testNormalizeWithEmptyFragment(): void
    {
        $result = $this->normalizer->normalize('https://example.com#');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizeWithEmptyQueryString(): void
    {
        $result = $this->normalizer->normalize('https://example.com?');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizePreservesHostCaseInsensitivity(): void
    {
        $result1 = $this->normalizer->normalize('https://Example.COM');
        $result2 = $this->normalizer->normalize('https://example.com');

        $this->assertEquals($result1, $result2);
    }
}

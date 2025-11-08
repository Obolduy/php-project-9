<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Url\Services\UrlNormalizer;
use PHPUnit\Framework\TestCase;

class UrlNormalizerTest extends TestCase
{
    private UrlNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new UrlNormalizer();
    }

    public function testNormalizeValidHttpUrl(): void
    {
        $result = $this->normalizer->normalize('http://example.com');
        $this->assertEquals('http://example.com', $result);
    }

    public function testNormalizeValidHttpsUrl(): void
    {
        $result = $this->normalizer->normalize('https://example.com');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizeUrlWithPath(): void
    {
        $result = $this->normalizer->normalize('https://example.com/path/to/page');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizeUrlWithQueryString(): void
    {
        $result = $this->normalizer->normalize('https://example.com/page?param=value');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizeUrlWithFragment(): void
    {
        $result = $this->normalizer->normalize('https://example.com/page#section');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizeUrlWithPort(): void
    {
        $result = $this->normalizer->normalize('https://example.com:8080/page');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizeConvertsToLowercase(): void
    {
        $result = $this->normalizer->normalize('HTTPS://EXAMPLE.COM');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizeTrimsWhitespace(): void
    {
        $result = $this->normalizer->normalize('  https://example.com  ');
        $this->assertEquals('https://example.com', $result);
    }

    public function testNormalizeReturnsNullForEmptyString(): void
    {
        $result = $this->normalizer->normalize('');
        $this->assertNull($result);
    }

    public function testNormalizeReturnsNullForWhitespaceOnly(): void
    {
        $result = $this->normalizer->normalize('   ');
        $this->assertNull($result);
    }

    public function testNormalizeReturnsNullForUrlWithoutScheme(): void
    {
        $result = $this->normalizer->normalize('example.com');
        $this->assertNull($result);
    }

    public function testNormalizeReturnsNullForUrlWithoutHost(): void
    {
        $result = $this->normalizer->normalize('https://');
        $this->assertNull($result);
    }

    public function testNormalizeReturnsNullForInvalidScheme(): void
    {
        $result = $this->normalizer->normalize('ftp://example.com');
        $this->assertNull($result);
    }

    public function testNormalizeReturnsNullForFileScheme(): void
    {
        $result = $this->normalizer->normalize('file:///path/to/file');
        $this->assertNull($result);
    }

    public function testNormalizeReturnsNullForMailtoScheme(): void
    {
        $result = $this->normalizer->normalize('mailto:test@example.com');
        $this->assertNull($result);
    }

    public function testNormalizeComplexValidUrl(): void
    {
        $result = $this->normalizer->normalize('HTTPS://WWW.EXAMPLE.COM:443/path?query=1#hash');
        $this->assertEquals('https://www.example.com', $result);
    }

    public function testNormalizeSubdomain(): void
    {
        $result = $this->normalizer->normalize('https://subdomain.example.com');
        $this->assertEquals('https://subdomain.example.com', $result);
    }

    public function testNormalizeReturnsNullForInvalidUrl(): void
    {
        $result = $this->normalizer->normalize('not a url at all');
        $this->assertNull($result);
    }
}

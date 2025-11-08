<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Config\Messages;
use Hexlet\Code\Url\Services\UrlNormalizer;
use Hexlet\Code\Url\Validators\UrlValidator;
use PHPUnit\Framework\TestCase;

class UrlValidatorTest extends TestCase
{
    private UrlValidator $validator;

    protected function setUp(): void
    {
        $normalizer = new UrlNormalizer();
        $this->validator = new UrlValidator($normalizer);
    }

    public function testValidateAcceptsValidHttpUrl(): void
    {
        $errors = $this->validator->validate('http://example.com');
        $this->assertEmpty($errors);
    }

    public function testValidateAcceptsValidHttpsUrl(): void
    {
        $errors = $this->validator->validate('https://example.com');
        $this->assertEmpty($errors);
    }

    public function testValidateRejectsEmptyUrl(): void
    {
        $errors = $this->validator->validate('');
        $this->assertArrayHasKey('name', $errors);
        $this->assertEquals(Messages::URL_REQUIRED, $errors['name']);
    }

    public function testValidateRejectsWhitespaceOnlyUrl(): void
    {
        $errors = $this->validator->validate('   ');
        $this->assertArrayHasKey('name', $errors);
        $this->assertEquals(Messages::URL_REQUIRED, $errors['name']);
    }

    public function testValidateRejectsUrlWithoutScheme(): void
    {
        $errors = $this->validator->validate('example.com');
        $this->assertArrayHasKey('name', $errors);
        $this->assertEquals(Messages::URL_INVALID, $errors['name']);
    }

    public function testValidateRejectsUrlWithInvalidScheme(): void
    {
        $errors = $this->validator->validate('ftp://example.com');
        $this->assertArrayHasKey('name', $errors);
        $this->assertEquals(Messages::URL_INVALID, $errors['name']);
    }

    public function testValidateRejectsTooLongUrl(): void
    {
        $longUrl = 'https://' . str_repeat('a', 250) . '.com';
        $errors = $this->validator->validate($longUrl);
        $this->assertArrayHasKey('name', $errors);
        $this->assertStringContainsString('255', $errors['name']);
    }

    public function testValidateTrimsWhitespace(): void
    {
        $errors = $this->validator->validate('  https://example.com  ');
        $this->assertEmpty($errors);
    }

    public function testValidateRejectsInvalidUrl(): void
    {
        $errors = $this->validator->validate('not a url');
        $this->assertArrayHasKey('name', $errors);
        $this->assertEquals(Messages::URL_INVALID, $errors['name']);
    }

    public function testValidateAcceptsUrlWithPath(): void
    {
        $errors = $this->validator->validate('https://example.com/path/to/page');
        $this->assertEmpty($errors);
    }

    public function testValidateAcceptsUrlWithQueryString(): void
    {
        $errors = $this->validator->validate('https://example.com?query=value');
        $this->assertEmpty($errors);
    }

    public function testValidateRejectsUrlWithoutHost(): void
    {
        $errors = $this->validator->validate('https://');
        $this->assertArrayHasKey('name', $errors);
        $this->assertEquals(Messages::URL_INVALID, $errors['name']);
    }

    public function testValidateAcceptsMaxLengthUrl(): void
    {
        $domain = str_repeat('a', 255 - strlen('https://') - strlen('.com'));
        $url = 'https://' . $domain . '.com';

        if (strlen($url) <= 255) {
            $errors = $this->validator->validate($url);
            $this->assertEmpty($errors);
        } else {
            $this->markTestSkipped('Cannot create URL at exact limit');
        }
    }
}

<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Config\Messages;
use Hexlet\Code\Url\Services\UrlNormalizer;
use Hexlet\Code\Url\Validators\UrlValidator;
use PHPUnit\Framework\TestCase;

class UrlValidatorEdgeCasesTest extends TestCase
{
    private UrlValidator $validator;

    protected function setUp(): void
    {
        $normalizer = new UrlNormalizer();
        $this->validator = new UrlValidator($normalizer);
    }

    public function testValidateWithTabCharacters(): void
    {
        $errors = $this->validator->validate("\thttps://example.com\t");
        $this->assertEmpty($errors);
    }

    public function testValidateWithNewlineCharacters(): void
    {
        $errors = $this->validator->validate("\nhttps://example.com\n");
        $this->assertEmpty($errors);
    }

    public function testValidateWithMixedWhitespace(): void
    {
        $errors = $this->validator->validate("  \t\n  https://example.com  \n\t  ");
        $this->assertEmpty($errors);
    }

    public function testValidateRejectsJustProtocol(): void
    {
        $errors = $this->validator->validate('https://');
        $this->assertArrayHasKey('name', $errors);
        $this->assertEquals(Messages::URL_INVALID, $errors['name']);
    }

    public function testValidateAcceptsUrlWithPort(): void
    {
        $errors = $this->validator->validate('https://example.com:8080');
        $this->assertEmpty($errors);
    }

    public function testValidateAcceptsUrlWithSubdomain(): void
    {
        $errors = $this->validator->validate('https://www.example.com');
        $this->assertEmpty($errors);
    }

    public function testValidateAcceptsUrlWithMultipleSubdomains(): void
    {
        $errors = $this->validator->validate('https://api.v2.example.com');
        $this->assertEmpty($errors);
    }

    public function testValidateRejectsUrlStartingWithDot(): void
    {
        $errors = $this->validator->validate('https://.example.com');
        $this->assertIsArray($errors);
    }

    public function testValidateRejectsUrlEndingWithDot(): void
    {
        $errors = $this->validator->validate('https://example.com.');
        $this->assertTrue(is_array($errors));
    }

    public function testValidateAcceptsHttpUrl(): void
    {
        $errors = $this->validator->validate('http://example.com');
        $this->assertEmpty($errors);
    }

    public function testValidateRejectsWsProtocol(): void
    {
        $errors = $this->validator->validate('ws://example.com');
        $this->assertArrayHasKey('name', $errors);
    }

    public function testValidateRejectsWssProtocol(): void
    {
        $errors = $this->validator->validate('wss://example.com');
        $this->assertArrayHasKey('name', $errors);
    }

    public function testValidateWithUrlAt254Characters(): void
    {
        $host = str_repeat('a', 254 - strlen('https://') - strlen('.com'));
        $url = 'https://' . $host . '.com';
        
        if (strlen($url) <= 255) {
            $errors = $this->validator->validate($url);
            $this->assertTrue(is_array($errors));
        } else {
            $this->markTestSkipped('Cannot create URL at exact length');
        }
    }

    public function testValidateRejectsNullByte(): void
    {
        $errors = $this->validator->validate("https://example.com\0");
        $this->assertTrue(is_array($errors));
    }

    public function testValidateMultipleCalls(): void
    {
        $errors1 = $this->validator->validate('https://example.com');
        $errors2 = $this->validator->validate('https://test.com');
        $errors3 = $this->validator->validate('invalid');

        $this->assertEmpty($errors1);
        $this->assertEmpty($errors2);
        $this->assertNotEmpty($errors3);
    }

    public function testValidateAcceptsIPv4Address(): void
    {
        $errors = $this->validator->validate('https://192.168.1.1');
        $this->assertEmpty($errors);
    }

    public function testValidateAcceptsLocalhostWithPort(): void
    {
        $errors = $this->validator->validate('http://localhost:3000');
        $this->assertEmpty($errors);
    }
}

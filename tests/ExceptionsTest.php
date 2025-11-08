<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Common\Exceptions\DataBaseConnectionException;
use Hexlet\Code\Config\ExceptionsTexts;
use Hexlet\Code\Url\Exceptions\UrlStoreValidationException;
use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{
    public function testDataBaseConnectionExceptionWithDefaultMessage(): void
    {
        $exception = new DataBaseConnectionException();

        $this->assertEquals(ExceptionsTexts::EMPTY_DATABASE_URL, $exception->getMessage());
    }

    public function testDataBaseConnectionExceptionWithCustomMessage(): void
    {
        $customMessage = 'Custom database error';
        $exception = new DataBaseConnectionException($customMessage);

        $this->assertEquals($customMessage, $exception->getMessage());
    }

    public function testUrlStoreValidationExceptionWithDefaultMessage(): void
    {
        $exception = new UrlStoreValidationException();

        $this->assertEquals(ExceptionsTexts::EMPTY_DATABASE_URL, $exception->getMessage());
    }

    public function testUrlStoreValidationExceptionWithCustomMessage(): void
    {
        $customMessage = 'Custom validation error';
        $exception = new UrlStoreValidationException($customMessage);

        $this->assertEquals($customMessage, $exception->getMessage());
    }

    public function testExceptionsAreInstancesOfException(): void
    {
        $dbException = new DataBaseConnectionException();
        $urlException = new UrlStoreValidationException();

        $this->assertInstanceOf(\Exception::class, $dbException);
        $this->assertInstanceOf(\Exception::class, $urlException);
    }

    public function testExceptionsCanBeThrown(): void
    {
        $this->expectException(DataBaseConnectionException::class);
        throw new DataBaseConnectionException('Test exception');
    }

    public function testExceptionTextsConstants(): void
    {
        $this->assertIsString(ExceptionsTexts::EMPTY_DATABASE_URL);
        $this->assertIsString(ExceptionsTexts::INVALID_DATABASE_URL);
        $this->assertIsString(ExceptionsTexts::URL_ID_IS_NULL_AT_CREATION);
        $this->assertIsString(ExceptionsTexts::URL_ID_IS_NULL_AFTER_SAVE);

        $this->assertNotEmpty(ExceptionsTexts::EMPTY_DATABASE_URL);
        $this->assertNotEmpty(ExceptionsTexts::INVALID_DATABASE_URL);
        $this->assertNotEmpty(ExceptionsTexts::URL_ID_IS_NULL_AT_CREATION);
        $this->assertNotEmpty(ExceptionsTexts::URL_ID_IS_NULL_AFTER_SAVE);
    }
}

<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Common\Connection;
use Hexlet\Code\Common\Exceptions\DataBaseConnectionException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ConnectionTest extends TestCase
{
    protected function tearDown(): void
    {
        $reflection = new ReflectionClass(Connection::class);
        $property = $reflection->getProperty('pdo');
        $property->setAccessible(true);
        $property->setValue(null, null);
    }

    public function testGetThrowsExceptionWhenDatabaseUrlIsEmpty(): void
    {
        $_ENV['DATABASE_URL'] = '';

        $this->expectException(DataBaseConnectionException::class);

        Connection::get();
    }

    public function testGetThrowsExceptionWhenDatabaseUrlIsInvalid(): void
    {
        $_ENV['DATABASE_URL'] = 'http://';

        $this->expectException(DataBaseConnectionException::class);

        Connection::get();
    }

    public function testGetReturnsValidDatabaseUrl(): void
    {
        $validUrl = 'postgresql://user:pass@localhost:5432/testdb';
        $_ENV['DATABASE_URL'] = $validUrl;

        try {
            Connection::get();
            $this->fail('Expected connection to fail with test credentials');
        } catch (\PDOException $e) {
            $this->assertTrue(true);
        } catch (DataBaseConnectionException $e) {
            $this->fail('URL parsing should succeed: ' . $e->getMessage());
        }
    }

    public function testGetUsesFallbackValuesWhenUrlComponentsMissing(): void
    {
        $_ENV['DATABASE_URL'] = 'postgresql://localhost/testdb';
        $_ENV['DB_USER'] = 'fallback_user';
        $_ENV['DB_PASSWORD'] = 'fallback_pass';
        $_ENV['DB_PORT'] = '5432';
        $_ENV['DB_HOST'] = 'fallback_host';

        try {
            Connection::get();
            $this->fail('Expected connection to fail with test credentials');
        } catch (\PDOException $e) {
            $this->assertTrue(true);
        } catch (DataBaseConnectionException $e) {
            $this->fail('Should parse URL with fallback values: ' . $e->getMessage());
        }
    }
}

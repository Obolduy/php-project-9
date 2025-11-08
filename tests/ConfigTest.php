<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private array $originalEnv;
    private array $originalServer;

    protected function setUp(): void
    {
        $this->originalEnv = $_ENV;
        $this->originalServer = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_ENV = $this->originalEnv;
        $_SERVER = $this->originalServer;
    }

    public function testGetReturnsEnvValue(): void
    {
        $_ENV['TEST_KEY'] = 'test_value';

        $value = Config::get('TEST_KEY');

        $this->assertEquals('test_value', $value);
    }

    public function testGetReturnsServerValueWhenEnvNotSet(): void
    {
        unset($_ENV['TEST_KEY']);
        $_SERVER['TEST_KEY'] = 'server_value';

        $value = Config::get('TEST_KEY');

        $this->assertEquals('server_value', $value);
    }

    public function testGetReturnsDefaultWhenKeyNotFound(): void
    {
        unset($_ENV['NONEXISTENT_KEY']);
        unset($_SERVER['NONEXISTENT_KEY']);

        $value = Config::get('NONEXISTENT_KEY', 'default_value');

        $this->assertEquals('default_value', $value);
    }

    public function testGetReturnsNullWhenKeyNotFoundAndNoDefault(): void
    {
        unset($_ENV['NONEXISTENT_KEY']);
        unset($_SERVER['NONEXISTENT_KEY']);

        $value = Config::get('NONEXISTENT_KEY');

        $this->assertNull($value);
    }

    public function testGetPrefersEnvOverServer(): void
    {
        $_ENV['TEST_KEY'] = 'env_value';
        $_SERVER['TEST_KEY'] = 'server_value';

        $value = Config::get('TEST_KEY');

        $this->assertEquals('env_value', $value);
    }

    public function testGetDatabaseName(): void
    {
        $_ENV['DB_NAME'] = 'test_database';

        $value = Config::getDatabaseName();

        $this->assertEquals('test_database', $value);
    }

    public function testGetDatabaseNameReturnsEmptyStringWhenNotSet(): void
    {
        unset($_ENV['DB_NAME']);
        unset($_SERVER['DB_NAME']);

        $value = Config::getDatabaseName();

        $this->assertEquals('', $value);
    }

    public function testGetDatabaseUser(): void
    {
        $_ENV['DB_USER'] = 'test_user';

        $value = Config::getDatabaseUser();

        $this->assertEquals('test_user', $value);
    }

    public function testGetDatabasePassword(): void
    {
        $_ENV['DB_PASSWORD'] = 'test_password';

        $value = Config::getDatabasePassword();

        $this->assertEquals('test_password', $value);
    }

    public function testGetDatabasePort(): void
    {
        $_ENV['DB_PORT'] = '5432';

        $value = Config::getDatabasePort();

        $this->assertEquals('5432', $value);
    }

    public function testGetDatabaseHost(): void
    {
        $_ENV['DB_HOST'] = 'localhost';

        $value = Config::getDatabaseHost();

        $this->assertEquals('localhost', $value);
    }

    public function testGetDatabaseUrl(): void
    {
        $_ENV['DATABASE_URL'] = 'postgresql://user:pass@localhost:5432/db';

        $value = Config::getDatabaseUrl();

        $this->assertEquals('postgresql://user:pass@localhost:5432/db', $value);
    }

    public function testGetHandlesDifferentTypes(): void
    {
        $_ENV['STRING_VALUE'] = 'string';
        $_ENV['NUMERIC_VALUE'] = '123';
        $_ENV['BOOL_VALUE'] = 'true';

        $this->assertEquals('string', Config::get('STRING_VALUE'));
        $this->assertEquals('123', Config::get('NUMERIC_VALUE'));
        $this->assertEquals('true', Config::get('BOOL_VALUE'));
    }
}

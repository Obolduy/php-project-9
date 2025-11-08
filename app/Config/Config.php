<?php

namespace Hexlet\Code\Config;

class Config
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    public static function getDatabaseName(): string
    {
        return self::get('DB_NAME', '');
    }

    public static function getDatabaseUser(): string
    {
        return self::get('DB_USER', '');
    }

    public static function getDatabasePassword(): string
    {
        return self::get('DB_PASSWORD', '');
    }

    public static function getDatabasePort(): string
    {
        return self::get('DB_PORT', '');
    }

    public static function getDatabaseHost(): string
    {
        return self::get('DB_HOST', '');
    }

    public static function getDatabaseUrl(): string
    {
        return self::get('DATABASE_URL', '');
    }
}

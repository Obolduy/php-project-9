<?php

namespace Hexlet\Code\Config;

class Config
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    public static function getDatabaseUrl(): string
    {
        return self::get('DATABASE_URL', '');
    }
}

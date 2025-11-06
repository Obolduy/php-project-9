<?php

namespace Hexlet\Code\Services;

class FlashService
{
    private const string FLASH_KEY = 'flash';

    public function set(string $key, mixed $value): void
    {
        $_SESSION[self::FLASH_KEY][$key] = $value;
    }

    public function get(string $key): mixed
    {
        $value = $_SESSION[self::FLASH_KEY][$key] ?? null;

        unset($_SESSION[self::FLASH_KEY][$key]);

        return $value;
    }

    public function has(): bool
    {
        return !empty($_SESSION[self::FLASH_KEY]);
    }

    public function getAll(): array
    {
        $flash = $_SESSION[self::FLASH_KEY] ?? [];

        unset($_SESSION[self::FLASH_KEY]);

        return $flash;
    }

    public function clear(): void
    {
        unset($_SESSION[self::FLASH_KEY]);
    }
}

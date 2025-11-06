<?php

namespace Hexlet\Code;

use Exception;
use Hexlet\Code\Config\Config;
use PDO;

class Connection
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            $databaseUrl = Config::getDatabaseUrl();

            if (empty($databaseUrl)) {
                throw new Exception('DATABASE_URL is not set');
            }

            $parsedUrl = parse_url($databaseUrl);

            if ($parsedUrl === false) {
                throw new Exception('Invalid DATABASE_URL format');
            }

            $host = $parsedUrl['host'] ?? 'localhost';
            $port = $parsedUrl['port'] ?? 5432;
            $dbname = ltrim($parsedUrl['path'] ?? '', '/');
            $username = $parsedUrl['user'] ?? '';
            $password = $parsedUrl['pass'] ?? '';

            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

            self::$pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$pdo;
    }
}

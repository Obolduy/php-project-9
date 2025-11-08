<?php

namespace Hexlet\Code\Common;

use Exception;
use Hexlet\Code\Common\Exceptions\DataBaseConnectionException;
use Hexlet\Code\Config\Config;
use Hexlet\Code\Config\ExceptionsTexts;
use PDO;

class Connection
{
    private static ?PDO $pdo = null;

    /**
     * @throws Exception
     */
    public static function get(): PDO
    {
        if (self::$pdo === null) {
            $databaseUrl = Config::getDatabaseUrl();

            if (empty($databaseUrl)) {
                throw new DataBaseConnectionException(ExceptionsTexts::EMPTY_DATABASE_URL);
            }

            $parsedUrl = parse_url($databaseUrl);

            if ($parsedUrl === false) {
                throw new DataBaseConnectionException(ExceptionsTexts::INVALID_DATABASE_URL);
            }

            $host = $parsedUrl['host'] ?? Config::getDatabaseHost();
            $port = $parsedUrl['port'] ?? Config::getDatabasePort();
            $dbname = ltrim($parsedUrl['path'] ?? '', '/') ?? Config::getDatabaseName();
            $username = $parsedUrl['user'] ?? Config::getDatabaseUser();
            $password = $parsedUrl['pass'] ?? Config::getDatabasePassword();

            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

            self::$pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$pdo;
    }
}

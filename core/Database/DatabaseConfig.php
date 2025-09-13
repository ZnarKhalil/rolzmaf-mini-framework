<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Database;

use PDO;
use RuntimeException;

class DatabaseConfig
{
    public static function makePdo(): PDO
    {
        $driver = $_ENV['DB_DRIVER'] ?? 'mysql';

        return match ($driver) {
            'mysql'  => self::mysql(),
            'sqlite' => self::sqlite(),
            default  => throw new RuntimeException("Unsupported DB driver: {$driver}"),
        };
    }

    private static function mysql(): PDO
    {
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $db   = $_ENV['DB_NAME'] ?? 'framework';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? '';

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        return new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    private static function sqlite(): PDO
    {
        $path = $_ENV['DB_PATH'] ?? __DIR__ . '/../../storage/database.sqlite';
        $dsn  = 'sqlite:' . $path;

        return new PDO($dsn, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
}

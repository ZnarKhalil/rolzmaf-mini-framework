<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Logging;

use Core\Logging\Drivers\LoggerInterface;

class Logger
{
    private static LoggerInterface $driver;

    public static function setDriver(LoggerInterface $driver): void
    {
        self::$driver = $driver;
    }

    public static function log(string $level, string $message, array $context = []): void
    {
        self::$driver->log($level, $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::log('DEBUG', $message, $context);
    }
}

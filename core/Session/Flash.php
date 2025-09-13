<?php

declare(strict_types=1);

namespace Core\Session;

class Flash
{
    protected static string $flashKey = '_flash';

    public static function set(string $key, mixed $value): void
    {
        Session::start();
        $_SESSION[self::$flashKey][$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        Session::start();
        $value = $_SESSION[self::$flashKey][$key] ?? $default;
        unset($_SESSION[self::$flashKey][$key]);

        return $value;
    }

    public static function has(string $key): bool
    {
        Session::start();

        return isset($_SESSION[self::$flashKey][$key]);
    }

    public static function all(): array
    {
        Session::start();
        $data = $_SESSION[self::$flashKey] ?? [];
        unset($_SESSION[self::$flashKey]);

        return $data;
    }
}

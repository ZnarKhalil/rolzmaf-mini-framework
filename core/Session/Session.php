<?php


declare(strict_types=1);

namespace Core\Session;

class Session
{
    protected static bool $started = false;

    public static function start(): void
    {
        if (!self::$started && session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
            self::$started = true;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();

        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();

        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function all(): array
    {
        self::start();

        return $_SESSION;
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
            $_SESSION      = [];
            self::$started = false;
        }
    }
}

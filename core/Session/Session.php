<?php

declare(strict_types=1);

namespace Core\Session;

use Core\Config\Config;

class Session
{
    protected static bool $started = false;

    public static function start(): void
    {
        if (!self::$started && session_status() !== PHP_SESSION_ACTIVE) {
            $env    = (string) Config::get('env', 'local');
            $appUrl = (string) Config::get('url', 'http://localhost');
            $cfg    = (array) Config::get('cookie', []);

            $schemeSecure = str_starts_with(strtolower($appUrl), 'https://');
            $secure       = (bool) ($cfg['secure'] ?? ($env === 'production' || $schemeSecure));
            $httpOnly     = (bool) ($cfg['httponly'] ?? true);
            $sameSite     = (string) ($cfg['samesite'] ?? 'Lax');
            $path         = (string) ($cfg['path'] ?? '/');

            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => $path,
                'secure'   => $secure,
                'httponly' => $httpOnly,
                'samesite' => $sameSite,
            ]);

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

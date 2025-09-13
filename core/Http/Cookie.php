<?php


declare(strict_types=1);

namespace Core\Http;

class Cookie
{
    public static function set(string $name, string $value, int $minutes = 60): void
    {
        setcookie($name, $value, [
            'expires'  => time() + ($minutes * 60),
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    public static function get(string $name, mixed $default = null): mixed
    {
        return $_COOKIE[$name] ?? $default;
    }

    public static function delete(string $name): void
    {
        setcookie($name, '', time() - 3600, '/');
        unset($_COOKIE[$name]);
    }
}

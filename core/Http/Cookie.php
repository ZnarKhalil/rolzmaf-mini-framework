<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Config\Config;

class Cookie
{
    public static function set(string $name, string $value, int $minutes = 60): void
    {
        $env    = (string) Config::get('env', 'local');
        $appUrl = (string) Config::get('url', 'http://localhost');
        $cfg    = (array) Config::get('cookie', []);

        $schemeSecure = str_starts_with(strtolower($appUrl), 'https://');
        $secure       = (bool) ($cfg['secure'] ?? ($env === 'production' || $schemeSecure));
        $httpOnly     = (bool) ($cfg['httponly'] ?? true);
        $sameSite     = (string) ($cfg['samesite'] ?? 'Lax');
        $path         = (string) ($cfg['path'] ?? '/');

        setcookie($name, $value, [
            'expires'  => time() + ($minutes * 60),
            'path'     => $path,
            'secure'   => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite,
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

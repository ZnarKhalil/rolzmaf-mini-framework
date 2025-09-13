<?php


declare(strict_types=1);

namespace Core\Config;

class Config
{
    protected static array $items = [];

    public static function load(string $file): void
    {
        if (file_exists($file)) {
            self::$items = require $file;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $keys  = explode('.', $key);
        $value = self::$items;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}

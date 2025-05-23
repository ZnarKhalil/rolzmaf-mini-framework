<?php
namespace Core\Storage;

class StorageManager
{
    private static StorageInterface $driver;

    public static function setDriver(StorageInterface $driver): void
    {
        self::$driver = $driver;
    }

    public static function put(string $path, string $contents): bool
    {
        return self::$driver->put($path, $contents);
    }

    public static function get(string $path): ?string
    {
        return self::$driver->get($path);
    }

    public static function delete(string $path): bool
    {
        return self::$driver->delete($path);
    }

    public static function exists(string $path): bool
    {
        return self::$driver->exists($path);
    }

    public static function url(string $path): string
    {
        return self::$driver->url($path);
    }
}
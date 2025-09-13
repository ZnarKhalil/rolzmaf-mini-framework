<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Storage;

class DiskStorage implements StorageInterface
{
    public function __construct(
        private readonly string $root = __DIR__ . '/../../storage/files'
    ) {
    }

    public function put(string $path, string $contents): bool
    {
        $fullPath = $this->fullPath($path);
        $dir      = dirname($fullPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        return file_put_contents($fullPath, $contents) !== false;
    }

    public function get(string $path): ?string
    {
        return $this->exists($path) ? file_get_contents($this->fullPath($path)) : null;
    }

    public function delete(string $path): bool
    {
        return $this->exists($path) ? unlink($this->fullPath($path)) : false;
    }

    public function exists(string $path): bool
    {
        return file_exists($this->fullPath($path));
    }

    public function url(string $path): string
    {
        return '/storage/' . ltrim($path, '/');
    }

    private function fullPath(string $path): string
    {
        return rtrim($this->root, '/') . '/' . ltrim($path, '/');
    }
}

<?php
namespace Core\Storage;

interface StorageInterface
{
    public function put(string $path, string $contents): bool;
    public function get(string $path): ?string;
    public function delete(string $path): bool;
    public function exists(string $path): bool;
    public function url(string $path): string;
}
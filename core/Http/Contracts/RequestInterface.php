<?php
namespace Core\Http\Contracts;

interface RequestInterface
{
    public function method(): string;
    public function uri(): string;
    public function query(string $key = null, mixed $default = null): mixed;
    public function input(string $key = null, mixed $default = null): mixed;
    public function header(string $key = null, mixed $default = null): mixed;
    public function json(): array;
    public function all(): array;
}
<?php
namespace Core\Console;

class Input
{
    public function __construct(
        private readonly array $argv
    ) {}

    public function argument(int $index, mixed $default = null): mixed
    {
        return $this->argv[$index] ?? $default;
    }

    public function all(): array
    {
        return $this->argv;
    }
}
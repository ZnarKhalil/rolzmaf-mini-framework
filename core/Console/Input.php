<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Console;

class Input
{
    public function __construct(
        private readonly array $argv
    ) {
    }

    public function argument(int $index, mixed $default = null): mixed
    {
        return $this->argv[$index] ?? $default;
    }

    public function all(): array
    {
        return $this->argv;
    }
}

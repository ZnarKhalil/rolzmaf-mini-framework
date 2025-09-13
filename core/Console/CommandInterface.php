<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Console;

interface CommandInterface
{
    public function name(): string;
    public function description(): string;
    public function execute(Input $input): int; // return exit code
}

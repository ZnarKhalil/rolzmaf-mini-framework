<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Logging\Drivers;

interface LoggerInterface
{
    public function log(string $level, string $message, array $context = []): void;
}

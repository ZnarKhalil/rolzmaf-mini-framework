<?php
declare(strict_types=1);

namespace Core\Logging\Drivers;

interface LoggerInterface
{
    public function log(string $level, string $message, array $context = []): void;
}
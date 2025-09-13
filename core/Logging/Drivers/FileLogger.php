<?php

declare(strict_types=1);

namespace Core\Logging\Drivers;

class FileLogger implements LoggerInterface
{
    private string $file;

    public function __construct(string $filePath)
    {
        $this->file = $filePath;
    }

    public function log(string $level, string $message, array $context = []): void
    {
        $timestamp  = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context, JSON_UNESCAPED_SLASHES) : '';
        $log        = "[$timestamp] $level: $message " . $contextStr . PHP_EOL;

        file_put_contents($this->file, $log, FILE_APPEND);
    }
}

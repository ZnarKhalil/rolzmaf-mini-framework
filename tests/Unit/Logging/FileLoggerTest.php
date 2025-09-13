<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Tests\Unit\Logging;

use Core\Logging\Drivers\FileLogger;
use Core\Logging\Logger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FileLogger::class)]
final class FileLoggerTest extends TestCase
{
    private string $logFile;

    protected function setUp(): void
    {
        $this->logFile = __DIR__ . '/../../../storage/logs/test.log';

        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }

        Logger::setDriver(new FileLogger($this->logFile));
    }

    #[Test]
    public function it_logs_custom_level_message(): void
    {
        Logger::log('ERROR', 'This is a test log message');

        $this->assertFileExists($this->logFile);
        $contents = file_get_contents($this->logFile);
        $this->assertStringContainsString('ERROR', $contents);
        $this->assertStringContainsString('This is a test log message', $contents);
    }

    #[Test]
    public function it_logs_error_message(): void
    {
        Logger::error('Something failed', ['context' => 'unit-test']);

        $this->assertFileExists($this->logFile);
        $contents = file_get_contents($this->logFile);
        $this->assertStringContainsString('ERROR', $contents);
        $this->assertStringContainsString('Something failed', $contents);
        $this->assertStringContainsString('"context":"unit-test"', $contents);
    }

    #[Test]
    public function it_logs_info_message(): void
    {
        Logger::info('Informational event');

        $contents = file_get_contents($this->logFile);
        $this->assertStringContainsString('INFO', $contents);
        $this->assertStringContainsString('Informational event', $contents);
    }

    #[Test]
    public function it_logs_debug_message(): void
    {
        Logger::debug('Debugging event');

        $contents = file_get_contents($this->logFile);
        $this->assertStringContainsString('DEBUG', $contents);
        $this->assertStringContainsString('Debugging event', $contents);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }
}

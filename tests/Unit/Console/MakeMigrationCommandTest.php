<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use Core\Console\Commands\MakeMigrationCommand;
use Core\Console\Input;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MakeMigrationCommand::class)]
final class MakeMigrationCommandTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/rolzmaf_migrations_' . bin2hex(random_bytes(4));
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->tempDir);
        parent::tearDown();
    }

    #[Test]
    public function it_creates_migration_file(): void
    {
        $command = new MakeMigrationCommand($this->tempDir);
        $name    = 'test_users_table';
        $input   = new Input([$name]);

        ob_start();
        $exitCode = $command->execute($input);
        ob_end_clean();

        $files = glob($this->tempDir . '/*_' . $name . '.php');
        $this->assertNotEmpty($files);
        $this->assertSame(0, $exitCode);
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (scandir($dir) ?: [] as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}

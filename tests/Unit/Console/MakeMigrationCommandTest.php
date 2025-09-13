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
    #[Test]
    public function test_it_creates_migration_file(): void
    {
        $command = new MakeMigrationCommand();
        $name    = 'test_users_table';
        $input   = new Input([$name]);

        ob_start();
        $exitCode = $command->execute($input);
        ob_end_clean();

        $files = glob(__DIR__ . '/../../../database/migrations/*_' . $name . '.php');
        $this->assertNotEmpty($files);
        $this->assertSame(0, $exitCode);

        // Cleanup
        foreach ($files as $file) {
            unlink($file);
        }
    }
}

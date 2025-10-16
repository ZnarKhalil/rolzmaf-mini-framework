<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use Core\Console\Commands\MigrateRollbackCommand;
use Core\Console\Input;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MigrateRollbackCommand::class)]
final class MigrateRollbackCommandTest extends TestCase
{
    private array $envBackup = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->envBackup['DB_DRIVER'] = $_ENV['DB_DRIVER'] ?? null;
        $this->envBackup['DB_PATH']   = $_ENV['DB_PATH']   ?? null;

        $_ENV['DB_DRIVER'] = 'sqlite';
        $_ENV['DB_PATH']   = ':memory:';
    }

    protected function tearDown(): void
    {
        foreach ($this->envBackup as $key => $value) {
            if ($value === null) {
                unset($_ENV[$key]);
            } else {
                $_ENV[$key] = $value;
            }
        }

        parent::tearDown();
    }

    #[Test]
    public function it_handles_no_migrations_gracefully(): void
    {
        $command = new MigrateRollbackCommand();
        $input   = new Input([]);

        ob_start();
        $exitCode = $command->execute($input);
        ob_end_clean();

        $this->assertSame(0, $exitCode);
    }
}

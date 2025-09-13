<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

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
    #[Test]
    public function test_it_handles_no_migrations_gracefully(): void
    {
        $command = new MigrateRollbackCommand();
        $input   = new Input([]);

        ob_start();
        $exitCode = $command->execute($input);
        ob_end_clean();

        $this->assertSame(0, $exitCode);
    }
}

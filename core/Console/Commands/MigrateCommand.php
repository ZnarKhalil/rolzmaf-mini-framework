<?php


declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Console\CommandInterface;
use Core\Console\Input;
use Core\Database\DatabaseConfig;
use Core\Database\MigrationRunner;

class MigrateCommand implements CommandInterface
{
    public function name(): string
    {
        return 'migrate';
    }

    public function description(): string
    {
        return 'Run all pending database migrations';
    }

    public function execute(Input $input): int
    {
        $pdo    = DatabaseConfig::makePdo();
        $runner = new MigrationRunner($pdo);
        $runner->run();

        return 0;
    }
}

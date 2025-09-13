<?php


declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Console\CommandInterface;
use Core\Console\Input;

class MakeMigrationCommand implements CommandInterface
{
    public function name(): string
    {
        return 'make:migration';
    }

    public function description(): string
    {
        return 'Create a new migration file in database/migrations';
    }

    public function execute(Input $input): int
    {
        $name = $input->argument(0);
        if (!$name) {
            echo "❌  Migration name is required.\n";

            return 1;
        }

        $timestamp  = date('Y_m_d_His');
        $filename   = "{$timestamp}_{$name}.php";
        $targetPath = __DIR__ . '/../../../database/migrations/' . $filename;

        $stub = file_get_contents(__DIR__ . '/../../stubs/migration.stub');

        file_put_contents($targetPath, $stub);

        echo "✅  Migration created: database/migrations/{$filename}\n";

        return 0;
    }
}

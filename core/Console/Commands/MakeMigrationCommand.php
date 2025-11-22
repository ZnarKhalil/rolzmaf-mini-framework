<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Console\CommandInterface;
use Core\Console\Input;

class MakeMigrationCommand implements CommandInterface
{
    private string $migrationsPath;

    public function __construct(?string $migrationsPath = null)
    {
        $defaultPath          = dirname(__DIR__, 3) . '/database/migrations';
        $this->migrationsPath = rtrim($migrationsPath ?? $defaultPath, '/');
    }

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
        $targetPath = $this->migrationsPath . '/' . $filename;

        if (!is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0777, true);
        }

        $stub = file_get_contents(__DIR__ . '/../../Stubs/migration.stub');

        file_put_contents($targetPath, $stub);

        echo "✅  Migration created: {$filename}\n";

        return 0;
    }
}

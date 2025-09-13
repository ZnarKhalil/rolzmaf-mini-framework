<?php


declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Console\CommandInterface;
use Core\Console\Input;

class MakeModelCommand implements CommandInterface
{
    public function name(): string
    {
        return 'make:model';
    }

    public function description(): string
    {
        return 'Generate a new model in app/Models';
    }

    public function execute(Input $input): int
    {
        $name = $input->argument(0);
        if (!$name) {
            echo "❌  Model name is required.\n";

            return 1;
        }

        $rawName      = $input->argument(0);
        $parts        = explode('/', str_replace('\\', '/', $rawName));
        $className    = array_pop($parts);
        $relativePath = implode('/', $parts);
        $namespace    = $relativePath ? '\\' . str_replace('/', '\\', $relativePath) : '';

        $dir  = __DIR__ . '/../../../app/Models/' . $relativePath;
        $file = $dir . '/' . $className . '.php';

        if (file_exists($file)) {
            echo "⚠️  Model already exists: $file\n";

            return 1;
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $stub    = file_get_contents(__DIR__ . '/../../stubs/model.stub');
        $content = str_replace(
            ['{{class}}', '{{namespace}}', '{{table}}'],
            [$className, $namespace],
            $stub
        );

        file_put_contents($file, $content);
        echo "✅  Model created: app/Models/{$className}.php\n";

        return 0;
    }
}

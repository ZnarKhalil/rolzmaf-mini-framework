<?php


declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Console\CommandInterface;
use Core\Console\Input;

class MakeControllerCommand implements CommandInterface
{
    public function name(): string
    {
        return 'make:controller';
    }

    public function description(): string
    {
        return 'Generate a new controller in app/Controllers';
    }

    public function execute(Input $input): int
    {
        $rawName = $input->argument(0);
        if (!$rawName) {
            echo "❌  Controller name is required.\n";

            return 1;
        }

        // Support nested path
        $parts        = explode('/', str_replace('\\', '/', $rawName));
        $className    = array_pop($parts);
        $relativePath = implode('/', $parts);
        $namespace    = $relativePath ? '\\' . str_replace('/', '\\', $relativePath) : '';

        $dir  = __DIR__ . '/../../../app/Controllers/' . $relativePath;
        $file = $dir . '/' . $className . '.php';

        if (file_exists($file)) {
            echo "⚠️  Controller already exists: $file\n";

            return 1;
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $stub    = file_get_contents(__DIR__ . '/../../stubs/controller.stub');
        $content = str_replace(
            ['{{class}}', '{{namespace}}'],
            [$className, $namespace],
            $stub
        );

        file_put_contents($file, $content);
        echo "✅  Controller created at: app/Controllers/{$relativePath}/{$className}.php\n";

        return 0;
    }
}

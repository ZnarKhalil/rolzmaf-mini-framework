<?php


declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Console\CommandInterface;
use Core\Console\Input;

class MakeMiddlewareCommand implements CommandInterface
{
    public function name(): string
    {
        return 'make:middleware';
    }

    public function description(): string
    {
        return 'Generate a new middleware in app/Middlewares';
    }

    public function execute(Input $input): int
    {
        $name = $input->argument(0);
        if (!$name) {
            echo "❌  Middleware name is required.\n";

            return 1;
        }

        $rawName      = $input->argument(0);
        $parts        = explode('/', str_replace('\\', '/', $rawName));
        $className    = array_pop($parts);
        $relativePath = implode('/', $parts);
        $namespace    = $relativePath ? '\\' . str_replace('/', '\\', $relativePath) : '';

        $dir  = __DIR__ . '/../../../app/Middlewares/' . $relativePath;
        $file = $dir . '/' . $className . '.php';

        if (file_exists($file)) {
            echo "⚠️  Middleware already exists: $file\n";

            return 1;
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $stub    = file_get_contents(__DIR__ . '/../../stubs/middleware.stub');
        $content = str_replace(
            ['{{class}}', '{{namespace}}'],
            [$className, $namespace],
            $stub
        );

        file_put_contents($file, $content);
        echo "✅  Middleware created: app/Middlewares/{$className}.php\n";

        return 0;
    }
}

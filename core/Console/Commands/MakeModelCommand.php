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

        $stub = file_get_contents(__DIR__ . '/../../Stubs/model.stub');

        // Infer table name from class: snake_case and pluralize the last segment
        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));

        $pluralize = static function (string $word): string {
            // category -> categories (consonant + y)
            if (preg_match('/[^aeiou]y$/i', $word)) {
                return substr($word, 0, -1) . 'ies';
            }

            // toy -> toys (vowel + y)
            if (preg_match('/[aeiou]y$/i', $word)) {
                return $word . 's';
            }

            // bus -> buses, box -> boxes, church -> churches
            if (preg_match('/(s|sh|ch|x|z)$/i', $word)) {
                return $word . 'es';
            }

            // wolf -> wolves, knife -> knives
            if (preg_match('/(fe|f)$/i', $word)) {
                return preg_replace('/(fe|f)$/i', 'ves', $word);
            }

            // default
            return $word . 's';
        };

        $parts      = explode('_', $snake);
        $last       = array_pop($parts);
        $lastPlural = $pluralize($last);
        $table      = ($parts ? implode('_', $parts) . '_' : '') . $lastPlural;
        $content    = str_replace(
            ['{{class}}', '{{namespace}}', '{{table}}'],
            [$className, $namespace, $table],
            $stub
        );

        file_put_contents($file, $content);
        echo "✅  Model created: app/Models/{$className}.php\n";

        return 0;
    }
}

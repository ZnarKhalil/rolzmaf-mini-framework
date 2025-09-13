<?php


declare(strict_types=1);

namespace App\Console\Commands;

use Core\Console\CommandInterface;
use Core\Console\Input;

class ExampleCommand implements CommandInterface
{
    public function name(): string
    {
        return 'say:hello';
    }

    public function description(): string
    {
        return 'Outputs a hello message.';
    }

    public function execute(Input $input): int
    {
        $name = $input->argument(0, 'Developer');
        echo "Hello, $name!\n";

        return 0;
    }
}

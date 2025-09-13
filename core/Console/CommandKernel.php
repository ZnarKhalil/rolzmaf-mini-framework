<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Console;

class CommandKernel
{
    /** @var CommandInterface[] */
    private array $commands = [];

    public function register(CommandInterface $command): void
    {
        $this->commands[$command->name()] = $command;
    }

    public function run(array $argv): void
    {
        $input = new Input(array_slice($argv, 2));
        $name  = $argv[1] ?? null;

        if (!$name || !isset($this->commands[$name])) {
            $this->listCommands();
            exit(1);
        }

        $exitCode = $this->commands[$name]->execute($input);
        exit($exitCode);
    }

    private function listCommands(): void
    {
        echo "Available commands:\n";
        foreach ($this->commands as $name => $cmd) {
            echo "  $name\t" . $cmd->description() . "\n";
        }
    }
}

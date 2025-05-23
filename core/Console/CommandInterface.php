<?php
namespace Core\Console;

interface CommandInterface
{
    public function name(): string;
    public function description(): string;
    public function execute(Input $input): int; // return exit code
}
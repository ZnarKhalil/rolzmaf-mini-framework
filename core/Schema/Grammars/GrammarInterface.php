<?php
namespace Core\Schema\Grammars;

use Core\Schema\Table;

interface GrammarInterface
{
    public function compileCreate(string $table, Table $tableDef): string;
    public function compileDrop(string $table): string;
}
<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Schema\Grammars;

use Core\Schema\Table;

interface GrammarInterface
{
    public function compileCreate(string $table, Table $tableDef): string;
    public function compileDrop(string $table): string;
}

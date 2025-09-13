<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Schema\Grammars;

use Core\Schema\Table;

class SqliteGrammar implements GrammarInterface
{
    public function compileCreate(string $table, Table $definition): string
    {
        $columns = array_map(function ($col) {
            $sql = "\"{$col->name}\" {$col->type}";

            if ($col->primary && $col->autoIncrement) {
                $sql = "\"{$col->name}\" INTEGER PRIMARY KEY AUTOINCREMENT";
            } else {
                if ($col->length) {
                    $sql .= "({$col->length})";
                }
                if ($col->nullable === false) {
                    $sql .= ' NOT NULL';
                }
                if ($col->unique) {
                    $sql .= ' UNIQUE';
                }
            }

            return $sql;
        }, $definition->columns);

        return "CREATE TABLE \"$table\" (\n  " . implode(",\n  ", $columns) . "\n);";
    }

    public function compileDrop(string $table): string
    {
        return "DROP TABLE IF EXISTS \"$table\";";
    }
}

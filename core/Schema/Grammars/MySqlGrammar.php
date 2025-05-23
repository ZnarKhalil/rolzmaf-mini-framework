<?php
namespace Core\Schema\Grammars;

use Core\Schema\Table;

class MySqlGrammar implements GrammarInterface
{
    public function compileCreate(string $table, Table $definition): string
{
    $columnSql = array_map(fn($col) => $col->toSql(), $definition->columns);
    $indexSql = $definition->indexes ?? [];

    // foreign key constraints
    $foreignKeys = [];
    foreach ($definition->columns as $col) {
        if ($col->isForeign && $col->references && $col->on) {
            $fkName = "fk_{$table}_{$col->name}";
            $foreignKeys[] = "CONSTRAINT `{$fkName}` FOREIGN KEY (`{$col->name}`) REFERENCES `{$col->on}`(`{$col->references}`)";
        }
    }

    $allSql = array_merge($columnSql, $indexSql, $foreignKeys);
    $body = implode(",\n  ", $allSql);

    return <<<SQL
        CREATE TABLE `{$table}` (
        {$body}
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        SQL;
    }

    public function compileDrop(string $table): string
    {
        return "DROP TABLE IF EXISTS `$table`;";
    }
}
<?php


declare(strict_types=1);

namespace Core\Schema;

class Table
{
    public array $columns = [];
    public array $indexes = [];

    public function id(string $name = 'id'): self
    {
        $this->columns[] = new ColumnDefinition($name, 'INT', null, false, false, true, true);

        return $this;
    }

    public function string(string $name, int $length = 255): ColumnDefinition
    {
        $column          = new ColumnDefinition($name, 'VARCHAR', $length);
        $this->columns[] = $column;

        return $column;
    }

    public function timestamps(): self
    {
        $this->columns[] = new ColumnDefinition('created_at', 'TIMESTAMP', null, false);
        $this->columns[] = new ColumnDefinition('updated_at', 'TIMESTAMP', null, false);

        return $this;
    }

    public function text(string $name): ColumnDefinition
    {
        $column          = new ColumnDefinition($name, 'TEXT');
        $this->columns[] = $column;

        return $column;
    }

    public function boolean(string $name): ColumnDefinition
    {
        $column          = new ColumnDefinition($name, 'TINYINT', 1);
        $this->columns[] = $column;

        return $column;
    }

    public function integer(string $name): ColumnDefinition
    {
        $column          = new ColumnDefinition($name, 'INT');
        $this->columns[] = $column;

        return $column;
    }

    public function bigInteger(string $name): ColumnDefinition
    {
        $column          = new ColumnDefinition($name, 'BIGINT')->primary();
        $this->columns[] = $column;

        return $column;
    }

    public function foreignId(string $name): ColumnDefinition
    {
        $column            = new ColumnDefinition($name, 'INT');
        $column->isForeign = true;

        // Convention: user_id → REFERENCES users(id)
        $column->references = 'id';
        $column->on         = rtrim($name, '_id') . 's';

        $this->columns[] = $column;

        return $column;
    }

    public function index(string|array $columns): void
    {
        $columns         = (array) $columns;
        $indexName       = 'idx_' . implode('_', $columns);
        $this->indexes[] = "INDEX `$indexName` (" . implode(', ', array_map(fn ($c) => "`$c`", $columns)) . ')';
    }
}

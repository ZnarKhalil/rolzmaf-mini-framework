<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Schema;

class ColumnDefinition
{
    public function __construct(
        public string $name,
        public string $type,
        public ?int $length = null,
        public bool $nullable = false,
        public bool $unique = false,
        public bool $autoIncrement = false,
        public bool $primary = false,
        public bool $isForeign = false,
        public ?string $references = null,
        public ?string $on = null,
    ) {
    }

    public function toSql(): string
    {
        $sql = "`{$this->name}` {$this->type}";

        if ($this->length) {
            $sql .= "({$this->length})";
        }

        if ($this->autoIncrement) {
            $sql .= ' AUTO_INCREMENT';
        }

        if ($this->nullable === false) {
            $sql .= ' NOT NULL';
        } else {
            $sql .= ' NULL';
        }

        if ($this->unique) {
            $sql .= ' UNIQUE';
        }

        if ($this->primary) {
            $sql .= ' PRIMARY KEY';
        }

        return $sql;
    }

    public function nullable(): static
    {
        $this->nullable = true;

        return $this;
    }

    public function unique(): static
    {
        $this->unique = true;

        return $this;
    }

    public function primary(): static
    {
        $this->primary = true;

        return $this;
    }
}

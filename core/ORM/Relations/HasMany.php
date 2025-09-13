<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\ORM\Relations;

use Core\ORM\Model;

class HasMany extends Relation
{
    private string $related;

    public function __construct(
        Model $parent,
        string $related,
        string $foreignKey,
        string $localKey = 'id'
    ) {
        parent::__construct($parent, $foreignKey, $localKey);
        $this->related = $related;
    }

    public function load(array $parents): array
    {
        $keys    = array_column(array_map(fn ($p) => $p->toArray(), $parents), $this->localKey);
        $builder = ($this->related)::query();
        $rows    = $builder->whereIn($this->foreignKey, $keys)->fetch();

        $grouped = [];
        foreach ($rows as $row) {
            $key             = $row->{$this->foreignKey};
            $grouped[$key][] = $row;
        }

        return $grouped;
    }
}

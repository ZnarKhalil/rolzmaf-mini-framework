<?php
namespace Core\ORM\Relations;

use Core\ORM\Model;

class BelongsTo extends Relation
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
        // collect all user_id values from posts
        $foreignKeys = array_unique(array_map(fn($p) => $p->{$this->foreignKey}, $parents));

        if (empty($foreignKeys)) return [];

        $builder = ($this->related)::query();
        $relatedRows = $builder->whereIn($this->localKey, $foreignKeys)->fetch();

        // map by local key
        $mapped = [];
        foreach ($relatedRows as $row) {
            $key = $row->{$this->localKey};
            $mapped[$key] = $row;
        }

        return $mapped;
    }
}
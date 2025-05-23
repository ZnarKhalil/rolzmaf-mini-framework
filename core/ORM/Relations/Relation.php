<?php
namespace Core\ORM\Relations;

use Core\ORM\Model;

abstract class Relation
{
    public function __construct(
        protected Model $parent,
        public string $foreignKey,
        public string $localKey
    ) {}

    abstract public function load(array $parents): array;
}
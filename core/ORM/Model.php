<?php


declare(strict_types=1);

namespace Core\ORM;

use Core\Database\DatabaseConfig;

abstract class Model
{
    protected array $attributes            = [];
    protected static array $allowedColumns = [];
    public const ALLOWED_COLUMNS           = ['*', 'id', 'created_at', 'updated_at'];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @template TModel of Model
     * @return QueryBuilder<TModel>
     */
    public static function query(): QueryBuilder
    {
        $pdo = DatabaseConfig::makePdo();

        return new QueryBuilder(new static(), $pdo);
    }

    public function __set(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public static function table(): string
    {
        // default to plural snake_case of class name
        $class = (new \ReflectionClass(static::class))->getShortName();

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class)) . 's';
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public static function allowedColumns(): array
    {
        return static::$allowedColumns;
    }

}

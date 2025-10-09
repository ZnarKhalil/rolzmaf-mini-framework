<?php

declare(strict_types=1);

namespace Core\ORM;

use Core\Database\DatabaseConfig;

abstract class Model
{
    protected array $attributes            = [];
    protected static array $allowedColumns = ['*', 'id', 'created_at', 'updated_at'];

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
        // default to plural snake_case of class name (pluralize last segment)
        $class = new \ReflectionClass(static::class)->getShortName();
        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class));

        $pluralize = static function (string $word): string {
            if (preg_match('/[^aeiou]y$/i', $word)) {
                return substr($word, 0, -1) . 'ies';
            }
            if (preg_match('/[aeiou]y$/i', $word)) {
                return $word . 's';
            }
            if (preg_match('/(s|sh|ch|x|z)$/i', $word)) {
                return $word . 'es';
            }
            if (preg_match('/(fe|f)$/i', $word)) {
                return preg_replace('/(fe|f)$/i', 'ves', $word);
            }

            return $word . 's';
        };

        $parts      = explode('_', $snake);
        $last       = array_pop($parts);
        $lastPlural = $pluralize($last);

        return ($parts ? implode('_', $parts) . '_' : '') . $lastPlural;
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

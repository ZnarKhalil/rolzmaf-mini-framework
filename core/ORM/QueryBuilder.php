<?php

declare(strict_types=1);

namespace Core\ORM;

use Core\ORM\Relations\BelongsTo;
use PDO;

/**
 * @template TModel of \Core\ORM\Model
 */
class QueryBuilder
{
    private PDO $pdo;

    /** @var TModel */
    private Model $model;

    private array $columns = ['*'];
    private array $joins   = [];
    private array $wheres  = [];
    private array $orders  = [];
    private ?int $limit    = null;
    private array $with    = [];

    /**
     * @param TModel $model
     */
    public function __construct(Model $model, PDO $pdo)
    {
        $this->model = $model;
        $this->pdo   = $pdo;
    }

    public function select(string ...$columns): self
    {
        foreach ($columns as $column) {
            $this->ensureValidColumn($column, 'select');
        }

        $this->columns = $columns;

        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->validateJoinIdentifier($table, 'join table');
        $this->validateJoinIdentifier($first, 'join first');
        $this->validateJoinIdentifier($second, 'join second');

        if (!in_array($operator, ['=', '<>', '!=', '<', '<=', '>', '>='], true)) {
            throw new \InvalidArgumentException("Invalid join operator '$operator'");
        }

        $this->joins[] = "JOIN $table ON $first $operator $second";

        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->validateJoinIdentifier($table, 'leftJoin table');
        $this->validateJoinIdentifier($first, 'leftJoin first');
        $this->validateJoinIdentifier($second, 'leftJoin second');

        if (!in_array($operator, ['=', '<>', '!=', '<', '<=', '>', '>='], true)) {
            throw new \InvalidArgumentException("Invalid leftJoin operator '$operator'");
        }

        $this->joins[] = "LEFT JOIN $table ON $first $operator $second";

        return $this;
    }

    public function where(string $column, string $operator, mixed $value): self
    {
        $this->ensureValidColumn($column, 'where');
        $this->wheres[] = ['basic', $column, $operator, $value];

        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $this->ensureValidColumn($column, 'whereIn');

        if (empty($values)) {
            $this->wheres[] = ['raw', '1 = 0', []];
        } else {
            $placeholders   = implode(', ', array_fill(0, count($values), '?'));
            $this->wheres[] = ['raw', "$column IN ($placeholders)", $values];
        }

        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->ensureValidColumn($column, 'orderBy');

        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            throw new \InvalidArgumentException("Invalid order direction: $direction");
        }

        $this->orders[] = [$column, $direction];

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function with(string $relation): self
    {
        $this->with[] = $relation;

        return $this;
    }

    public function find(int|string $id): ?Model
    {
        $this->where($this->model::primaryKey(), '=', $id)->limit(1);

        return $this->first();
    }

    public function first(): ?Model
    {
        $this->limit(1);
        $results = $this->fetch();

        return $results[0] ?? null;
    }

    public function exists(): bool
    {
        $sql = 'SELECT 1 FROM `' . $this->model::table() . '`';

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        [$whereClause, $params] = $this->buildWhereClause();
        $sql .= $whereClause . ' LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }

    public function insert(array $data): bool
    {
        $table        = $this->model::table();
        $columns      = array_keys($data);
        $placeholders = array_map(fn ($k) => ":$k", $columns);
        $sql          = "INSERT INTO `$table` (" . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $stmt         = $this->pdo->prepare($sql);

        return $stmt->execute($data);
    }

    public function update(array $data): bool
    {
        if (empty($this->wheres)) {
            throw new \LogicException('Update requires a WHERE clause to avoid mass update.');
        }

        $table  = $this->model::table();
        $set    = [];
        $params = [];

        foreach ($data as $col => $val) {
            $this->ensureValidColumn($col, 'update');
            $set[]    = "`$col` = ?";
            $params[] = $val;
        }

        $sql                         = "UPDATE `$table` SET " . implode(', ', $set);
        [$whereClause, $whereParams] = $this->buildWhereClause();
        $sql .= $whereClause;
        $params = array_merge($params, $whereParams);

        return $this->pdo->prepare($sql)->execute($params);
    }

    public function delete(): bool
    {
        if (empty($this->wheres)) {
            throw new \LogicException('Delete requires a WHERE clause to avoid mass deletion.');
        }

        $table                  = $this->model::table();
        [$whereClause, $params] = $this->buildWhereClause();
        $sql                    = "DELETE FROM `$table`" . $whereClause;

        return $this->pdo->prepare($sql)->execute($params);
    }

    private function buildWhereClause(): array
    {
        $clauses = [];
        $params  = [];

        foreach ($this->wheres as $where) {
            $type = $where[0];

            if ($type === 'basic') {
                [$column, $operator, $value] = array_slice($where, 1);
                $clauses[]                   = "`$column` $operator ?";
                $params[]                    = $value;
            }

            if ($type === 'raw') {
                [$expr, $values] = array_slice($where, 1);
                $clauses[]       = $expr;
                $params          = array_merge($params, $values);
            }
        }

        return empty($clauses)
            ? ['', []]
            : [' WHERE ' . implode(' AND ', $clauses), $params];
    }

    public function fetch(): array
    {
        $sql = 'SELECT ' . implode(', ', $this->columns) . ' FROM `' . $this->model::table() . '`';

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        [$whereClause, $params] = $this->buildWhereClause();
        $sql .= $whereClause;

        if (!empty($this->orders)) {
            $orders = array_map(fn ($o) => "`$o[0]` $o[1]", $this->orders);
            $sql .= ' ORDER BY ' . implode(', ', $orders);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = array_map(fn ($row) => new ($this->model::class)($row), $rows);

        // Eager-load relations
        if (!empty($this->with)) {
            foreach ($this->with as $relation) {
                $method = [$this->model, $relation];

                if (!is_callable($method)) {
                    throw new \RuntimeException("Relation method $relation not defined.");
                }

                $relationInstance = $method();
                $loaded           = $relationInstance->load($models);

                foreach ($models as $model) {
                    if ($relationInstance instanceof BelongsTo) {
                        $fk                 = $model->{$relationInstance->foreignKey} ?? null;
                        $model->{$relation} = $loaded[$fk]                            ?? null;
                    } else {
                        $key                = $model->{$relationInstance->localKey} ?? null;
                        $model->{$relation} = $loaded[$key]                         ?? [];
                    }
                }
            }
        }

        return $models;
    }

    public function toSql(): array
    {
        $sql = 'SELECT ' . implode(', ', $this->columns) . ' FROM `' . $this->model::table() . '`';

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        [$whereClause, $params] = $this->buildWhereClause();
        $sql .= $whereClause;

        if (!empty($this->orders)) {
            $orders = array_map(fn ($o) => "`$o[0]` $o[1]", $this->orders);
            $sql .= ' ORDER BY ' . implode(', ', $orders);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        return ['sql' => $sql, 'params' => $params];
    }

    private function ensureValidColumn(string $column, string $context): void
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
            throw new \InvalidArgumentException("Invalid identifier syntax for $context: '$column'");
        }

        $allowed = $this->model::allowedColumns();
        if (is_array($allowed) && !in_array('*', $allowed, true) && !in_array($column, $allowed, true)) {
            throw new \InvalidArgumentException("Disallowed column '$column' in $context for model " . get_class($this->model));
        }
    }

    private function validateJoinIdentifier(string $identifier, string $context): void
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_.]*$/', $identifier)) {
            throw new \InvalidArgumentException("Invalid join identifier in $context: '$identifier'");
        }
    }
}

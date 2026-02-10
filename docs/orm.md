# ORM and Relations

## Model Base (`Core\ORM\Model`)

Every model extends `Core\ORM\Model`.

### Default behavior

- attributes stored in internal array
- magic getters/setters (`__get`, `__set`)
- `toArray()` for raw attributes
- `query()` returns a `QueryBuilder` bound to model and PDO connection
- default primary key: `id`
- default table naming: snake_case + pluralization of class name

Example:

```php
class User extends Model {}

User::table(); // users
```

### Column allow-list

`allowedColumns()` controls selectable/filterable/updatable columns in the query builder.

- default includes `['*', 'id', 'created_at', 'updated_at']`
- generated model stubs encourage explicit whitelisting

## QueryBuilder (`Core\ORM\QueryBuilder`)

### Read operations

- `select(string ...$columns)`
- `where(string $column, string $operator, mixed $value)`
- `whereIn(string $column, array $values)`
- `orderBy(string $column, string $direction = 'asc')`
- `limit(int $limit)`
- `find(int|string $id)`
- `first()`
- `fetch()`
- `exists()`
- `toSql(): array{sql:string,params:array}`

### Write operations

- `insert(array $data): bool`
- `update(array $data): bool` (requires WHERE)
- `delete(): bool` (requires WHERE)

### Join support

- `join(string $table, string $first, string $operator, string $second)`
- `leftJoin(string $table, string $first, string $operator, string $second)`

### Safety behavior

- identifier syntax validation for columns and joins
- disallowed columns throw `InvalidArgumentException`
- mass `update()` / `delete()` without `where()` throw `LogicException`
- values are always parameterized through prepared statements

### Eager loading

Use `with('relationName')` before `fetch()`:

```php
$posts = Post::query()->with('user')->limit(10)->fetch();
```

Supported relation types are currently `HasMany` and `BelongsTo`.

## Relations

### `HasMany`

```php
class User extends Model
{
    public static function posts(): HasMany
    {
        return new HasMany(new static(), Post::class, 'user_id');
    }
}
```

### `BelongsTo`

```php
class Post extends Model
{
    public static function user(): BelongsTo
    {
        return new BelongsTo(new static(), User::class, 'user_id');
    }
}
```

## Usage Examples

```php
// Read
$user = User::query()->find(1);

// Filter + sort
$rows = User::query()
    ->where('id', '>', 10)
    ->orderBy('id', 'desc')
    ->limit(20)
    ->fetch();

// Insert
User::query()->insert([
    'name' => 'Znar',
    'email' => 'znar@example.com',
    'password' => 'secret',
]);

// Guarded update
User::query()->where('id', '=', 1)->update(['name' => 'Updated']);
```

## Recommended Model Pattern

Explicitly define columns to keep query validation useful:

```php
class User extends Model
{
    protected static array $allowedColumns = [
        'id', 'name', 'email', 'password', 'created_at', 'updated_at',
    ];

    public static function table(): string
    {
        return 'users';
    }
}
```

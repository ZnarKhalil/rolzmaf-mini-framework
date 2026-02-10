# Schema Builder and Migrations

## Migration Lifecycle

Migrations live in `database/migrations/*.php` and return an anonymous class with:

- `up(): void`
- `down(): void`

Example:

```php
return new class {
    public function up(): void
    {
        Schema::instance()->create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::instance()->dropIfExists('users');
    }
};
```

## Migration Commands

- `php rolzmaf make:migration <name>`
- `php rolzmaf migrate`
- `php rolzmaf migrate:rollback`

`make:migration` creates timestamped files in `database/migrations/` using `core/Stubs/migration.stub`.

## Schema API

Entry point:

```php
Schema::instance()->create('table', function (Table $table) {
    // definitions
});

Schema::instance()->dropIfExists('table');
```

### `Table` methods

- `id(string $name = 'id')`
- `string(string $name, int $length = 255)`
- `text(string $name)`
- `boolean(string $name)`
- `integer(string $name)`
- `bigInteger(string $name)`
- `foreignId(string $name)`
- `timestamps()`
- `index(string|array $columns)`

### `ColumnDefinition` modifiers

- `nullable()`
- `unique()`
- `primary()`

## Grammar Backends

SQL generation is selected by `DB_DRIVER`:

- `sqlite` -> `Core\Schema\Grammars\SqliteGrammar`
- default -> `Core\Schema\Grammars\MySqlGrammar`

### MySQL grammar

- includes indexes and foreign keys
- emits InnoDB + utf8mb4 table DDL

### SQLite grammar

- simplified DDL
- no explicit foreign key/index emission from the grammar implementation

## Migration Tracking

`Core\Database\MigrationRunner`:

- ensures `migrations` table exists
- applies pending files in sorted order
- stores migration name + batch number
- runs each migration in a transaction

Rollback command behavior:

- selects latest batch from `migrations`
- executes `down()` for each migration in reverse insertion order
- deletes rollbacked migration records

## Notes for Contributors

- keep `up()` and `down()` symmetric
- use `Schema::reset()` in isolated tests when needed (singleton reset)
- avoid manual SQL unless schema API is insufficient

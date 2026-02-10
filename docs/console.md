# Console Commands and Code Generation

## Entrypoint

CLI entry is `rolzmaf`:

```bash
php rolzmaf <command> [arguments]
```

It bootstraps the app and runs `Core\Console\CommandKernel`.

## Command Kernel

`CommandKernel` responsibilities:

- register commands by name
- parse arguments via `Input`
- run selected command and exit with command return code
- print command list when command is missing/unknown

## Registered Commands

From `rolzmaf`:

- `make:migration`
- `migrate`
- `migrate:rollback`
- `make:controller`
- `make:model`
- `make:middleware`
- `example:run`

## Usage Reference

### `make:migration`

```bash
php rolzmaf make:migration create_orders_table
```

Creates: `database/migrations/YYYY_MM_DD_HHMMSS_create_orders_table.php`

### `migrate`

```bash
php rolzmaf migrate
```

Runs all pending migrations.

### `migrate:rollback`

```bash
php rolzmaf migrate:rollback
```

Rolls back last migration batch.

### `make:controller`

```bash
php rolzmaf make:controller Admin/UserController
```

Creates nested controller under `app/Controllers/Admin/UserController.php`.

### `make:model`

```bash
php rolzmaf make:model Blog/Post
```

Creates `app/Models/Blog/Post.php` using model stub and inferred snake_case plural table name.

### `make:middleware`

```bash
php rolzmaf make:middleware Auth/EnsureAdmin
```

Creates nested middleware under `app/Middlewares/Auth/EnsureAdmin.php`.

## Stub Templates

Command generators read stubs from `core/Stubs/`:

- `controller.stub`
- `model.stub`
- `middleware.stub`
- `migration.stub`

Customize these to change default generated code style.

## Adding a New Command

1. Implement `Core\Console\CommandInterface`.
2. Add class to autoloaded namespace.
3. Register instance in `rolzmaf`.

Minimal command skeleton:

```php
class HelloCommand implements CommandInterface
{
    public function name(): string { return 'hello'; }
    public function description(): string { return 'Print hello'; }

    public function execute(Input $input): int
    {
        echo "Hello\n";
        return 0;
    }
}
```

# Configuration, Storage, Logging, and Error Handling

## Configuration

`Core\Config\Config` loads an array from `config/app.php`.

Access pattern:

```php
Config::get('name');
Config::get('cookie.samesite', 'Lax');
```

`config/app.php` contains:

- `name`
- `env`
- `debug`
- `url`
- `cookie` options

Values are sourced from `$_ENV` loaded at bootstrap.

## Database Configuration

`Core\Database\DatabaseConfig::makePdo()` selects driver via `DB_DRIVER`:

- `mysql`
- `sqlite`

MySQL uses `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`.
SQLite uses `DB_PATH`.

## Storage

`Core\Storage\StorageManager` is a static facade over a configured `StorageInterface` driver.

Default driver is `DiskStorage(storage/files)` set during bootstrap.

Methods:

- `put(path, contents)`
- `get(path)`
- `delete(path)`
- `exists(path)`
- `url(path)`

`DiskStorage` behavior:

- creates directories automatically on write
- stores files under configured root
- maps URL to `/storage/<path>` (routing/public serving is app responsibility)

## Logging

`Core\Logging\Logger` exposes:

- `log(level, message, context)`
- `error(message, context)`
- `info(message, context)`
- `debug(message, context)`

Default driver is `Core\Logging\Drivers\FileLogger` to `storage/logs/app.log`.

File format:

```text
[YYYY-mm-dd HH:ii:ss] LEVEL: message {json-context}
```

## Exception and Error Handling

`Core\Support\ExceptionHandler::register()` configures:

- error handler -> logs PHP errors
- exception handler -> logs uncaught exceptions and returns HTTP 500 with `Internal Server Error`
- shutdown handler -> logs fatal errors

## Security Defaults

Session and cookie flags are hardened by default:

- `httponly` enabled unless overridden
- `secure` enabled in production or HTTPS URL
- `samesite` defaults to `Lax`

You can override through `config/app.php` under `cookie`.

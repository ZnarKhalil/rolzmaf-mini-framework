# Architecture Overview

## Runtime Flow

HTTP request flow:

1. `public/index.php` bootstraps the framework.
2. `bootstrap.php` loads env, config, logger, storage, and exception handlers.
3. A `Router` is initialized and hydrated from `routes/web.php`.
4. `HttpKernel` resolves a `Route` for the incoming `Request`.
5. `MiddlewarePipeline` executes global middleware + route middleware.
6. Controller action is invoked and returns a `Response`.
7. `Response::send()` writes status, headers, and body.

## Boot Sequence (`bootstrap.php`)

1. Composer autoload (`vendor/autoload.php`)
2. `.env` load via `vlucas/phpdotenv` (`safeLoad`)
3. App config load from `config/app.php`
4. Logger driver set to `FileLogger(storage/logs/app.log)`
5. Global `ExceptionHandler` registration
6. Storage driver set to `DiskStorage(storage/files)`

## Core Subsystems

- `core/Kernel`: request lifecycle orchestration
- `core/Routing`: route declaration and request resolution
- `core/Middleware`: middleware interface and pipeline composition
- `core/Http`: request/response/cookie abstractions
- `core/Session`: session state + flash data
- `core/ORM`: query builder, model base, relation loaders
- `core/Schema`: schema DSL + SQL grammar compilation
- `core/Database`: PDO creation + migration runner
- `core/Console`: command kernel + command interfaces/generators
- `core/Storage`: pluggable file-like storage
- `core/Logging`: logging facade + drivers
- `core/Support`: exception/error handling

## Design Characteristics

- Explicit wiring instead of heavy DI/container magic
- Static facades for shared infrastructure (`Logger`, `StorageManager`, `Config`, `Session`)
- Thin core classes with educational readability over abstraction depth
- Route matching by exact method + URI string
- ORM focused on common query workflows with relation eager loading

## Known Constraints

- No auto route model binding
- No dynamic route parameters (`/users/{id}` style)
- No service container / dependency injection container
- CLI command registration is manual in `rolzmaf`
- `routes/api.php` and `routes/console.php` exist but are currently unused by the default boot path

# Rolzmaf Documentation

This documentation is the primary technical reference for the framework and is intentionally separate from `README.md`.

## Documentation Map

1. [Getting Started](./getting-started.md)
2. [Architecture Overview](./architecture.md)
3. [HTTP, Routing, and Middleware](./http-routing-middleware.md)
4. [Request, Response, Cookies, and Session](./http-primitives.md)
5. [ORM and Relations](./orm.md)
6. [Schema Builder and Migrations](./schema-migrations.md)
7. [Console Commands and Code Generation](./console.md)
8. [Configuration, Storage, Logging, and Error Handling](./operations.md)
9. [Testing Strategy](./testing.md)

## Scope and Intent

Rolzmaf is an educational mini-framework inspired by Laravel-style conventions. It is optimized for readability and learning core framework concepts:

- HTTP request lifecycle and routing
- middleware pipelines
- persistence and lightweight ORM patterns
- migration-driven schema evolution
- CLI tooling and code generation

## Project Layout

```text
app/                  Application controllers, models, app-specific commands
config/               Config arrays loaded at bootstrap
database/migrations/  Migration classes
core/                 Framework internals
public/               HTTP entrypoint and static assets
routes/               Route definitions
storage/              Logs, files, sqlite DB
tests/                PHPUnit unit tests
rolzmaf               CLI entrypoint
bootstrap.php         Application bootstrap sequence
```

## Recommended Reading Order

1. `docs/getting-started.md`
2. `docs/architecture.md`
3. `docs/http-routing-middleware.md`
4. `docs/orm.md`
5. `docs/schema-migrations.md`

Use the rest as reference material.

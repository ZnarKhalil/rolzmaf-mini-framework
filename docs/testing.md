# Testing Strategy

## Test Framework

- PHPUnit (`phpunit/phpunit ^12.1`)
- Config: `phpunit.xml`
- Run: `composer test`

## Suite Focus

The suite is primarily unit tests under `tests/Unit` for framework internals:

- routing and kernel execution
- HTTP request/response and cookie behavior
- middleware pipeline and CSRF checks
- session + flash semantics
- ORM query builder and relations
- schema/table/column abstractions
- storage and logging
- console command behavior

## Examples of Verified Behavior

- `HttpKernel` returns 404 for unresolved routes
- controller actions can be invoked with or without request parameter
- CSRF blocks mutating requests without matching token
- query builder rejects disallowed columns
- update/delete require WHERE clause
- migrations can be generated and rollback command handles empty state

## Running Specific Tests

```bash
vendor/bin/phpunit --filter QueryBuilderTest
vendor/bin/phpunit tests/Unit/Middleware/CsrfMiddlewareTest.php
```

## Writing New Tests

Guidelines:

- prefer isolated unit tests around one class behavior
- use in-memory SQLite where database behavior is needed
- reset static singletons in teardown (`Schema::reset()` when applicable)
- assert both success and failure paths for security-sensitive code

## CI Notes

Composer scripts provide:

- `composer test`
- `composer fix:ci` for formatting checks

Framework contributors should run both before opening a PR.

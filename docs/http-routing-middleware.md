# HTTP, Routing, and Middleware

## Router API

`Core\Routing\Router` supports:

- `get(string $uri, array $action): Route`
- `post(string $uri, array $action): Route`
- `put(string $uri, array $action): Route`
- `delete(string $uri, array $action): Route`
- `addGlobalMiddleware(string $middlewareClass): void`
- `resolve(RequestInterface $request): ?Route`

Route action format:

```php
[ControllerClass::class, 'methodName']
```

Route-level middleware:

```php
$router->get('/admin', [AdminController::class, 'index'])
       ->middleware(AdminAuthMiddleware::class, AuditMiddleware::class);
```

## Route Matching Behavior

Resolution is strict and exact:

- HTTP method must exactly match
- URI must exactly match `Request::uri()` (query string stripped)
- no pattern matching or path params

If no route is found, kernel returns `404 Not Found`.

## HttpKernel Behavior

`Core\Kernel\HttpKernel::handle()` does:

1. Resolve route from router
2. Merge middleware in this order:
   - router global middleware first
   - route middleware second
3. Instantiate middleware classes and build pipeline
4. Resolve controller + method
5. Invoke controller action

Controller invocation supports two modes:

- zero-parameter actions (called directly)
- single parameter typed as `RequestInterface` (request injected)

Invalid controller/method returns 404.
Reflection failures return 500.

## Middleware Pipeline Semantics

`Core\Middleware\MiddlewarePipeline` uses reverse reduction:

- middleware are executed in the order added
- each middleware receives `process(RequestInterface $request, callable $next): ResponseInterface`

This mirrors common chain-of-responsibility patterns.

## Built-in System Middleware

### `SessionMiddleware`

- starts PHP session via `Session::start()`
- delegates immediately to next middleware

### `CsrfMiddleware`

- starts session
- allows `GET`, `HEAD`, `OPTIONS` without token checks
- validates token on mutating methods (`POST`, `PUT`, `DELETE`, etc.)
- expected token sources:
  - form input `_token`
  - header `x-csrf-token`
- mismatch returns `403 CSRF token mismatch`

Generate token for forms:

```php
use Core\Middleware\System\CsrfMiddleware;

$token = CsrfMiddleware::token();
```

## Practical Route Setup

```php
return function (Router $router) {
    $router->addGlobalMiddleware(SessionMiddleware::class);
    $router->addGlobalMiddleware(CsrfMiddleware::class);

    $router->get('/', [ExampleController::class, 'index']);

    $router->post('/posts', [PostController::class, 'store'])
           ->middleware(AuthMiddleware::class);
};
```

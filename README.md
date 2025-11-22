# Rolzmaf Mini Framework

![rolzmaf.png](public/rolzmaf.png)

![CI](https://github.com/ZnarKhalil/rolzmaf-mini-framework/actions/workflows/ci.yml/badge.svg?event=pull_request)
![PHP](https://img.shields.io/badge/php-8.4-blue)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

Rolzmaf is a compact PHP framework created for clarity and **learning**, intentionally inspired by the [Laravel](https://laravel.com) framework.

Note: Rolzmaf is educational. For production applications choose well-supported frameworks such as [Laravel](https://laravel.com) or [Symfony](https://symfony.com).

## Features

- Routing with middleware
  - Global and per‑route middleware
  - Simple, explicit route definitions
  - CSRF + Session middleware included

- HTTP primitives
  - Request: method, URI, query, input, headers
  - JSON helpers with content‑type gating and error reporting
  - Response: status, headers, body, plus `json()` and `redirect()` helpers

- Database + ORM
  - Query builder: `select`, `where`, `whereIn`, `orderBy`, `limit`, `first`, `find`, `exists`
  - Joins: `join`, `leftJoin`
  - Mutations: `insert`, `update`, `delete`
  - Relations: `hasMany`, `belongsTo` with eager loading via `with()`
  - Driver support: SQLite and MySQL

- Migrations + Schema
  - CLI: `migrate`, `migrate:rollback`, `make:migration`
  - Fluent schema builder, foreign keys (MySQL), indexes
  - Driver‑aware migrations table

- Console tools
  - Generators: `make:controller`, `make:model`, `make:middleware`
  - Stubs with nested namespaces

- Security & robustness
  - Prepared statements everywhere
  - Column allow‑listing with safe identifier checks
  - CSRF protection, session management with safe cookie defaults

## Quick Start

```bash
git clone https://github.com/ZnarKhalil/rolzmaf-mini-framework.git
cd rolzmaf-mini-framework
composer install
cp .env.example .env

mkdir -p storage/logs storage/files
touch storage/logs/app.log
```

Configure your database in `.env`:

```
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_DRIVER=sqlite
DB_PATH=storage/database.sqlite
```

Serve the app (any PHP 8.4+ server):

```bash
php -S localhost:8000 -t public
```

Visit http://localhost:8000.

## Routing

`routes/web.php`

```php
use Core\Routing\Router;
use App\Controllers\ExampleController;
use Core\Middleware\System\CsrfMiddleware;
use Core\Middleware\System\SessionMiddleware;

return function (Router $router) {
    $router->addGlobalMiddleware(SessionMiddleware::class);
    $router->addGlobalMiddleware(CsrfMiddleware::class);

    $router->get('/', [ExampleController::class, 'index']);
    $router->get('/about', [ExampleController::class, 'about']);
};
```

## Controllers

```php
namespace App\Controllers;

use Core\Http\Contracts\RequestInterface;
use Core\Http\Response;

class ExampleController
{
    // Supports optional Request dependency
    public function index(RequestInterface $request): Response
    {
        return new Response()->json(['message' => 'Hello from index']);
    }

    // Methods without parameters are also supported
    public function about(): Response
    {
        return new Response()->write('About page');
    }
}
```

## Request & Response

- Read inputs

```php
$method = $request->method();        // GET, POST, ...
$uri    = $request->uri();            // /path
$q      = $request->query('page');    // ?page=...
$in     = $request->input('name');    // POST body (form)
$hdr    = $request->header('x-id');   // Headers (normalized)
```

- JSON helpers

```php
// Respond JSON
return (new Response())->json(['ok' => true]);

// Redirect
return (new Response())->redirect('/login');

// Parse JSON only when content-type is application/json
$data = $request->json();            // [] when not JSON or empty body
$err  = $request->jsonError();       // error message or null
```

## ORM Examples

```php
use App\Models\User;

// Find
$user = User::query()->find(1);

// Insert
User::query()->insert([
    'name' => 'Znar',
    'email' => 'znar@example.com',
]);

// Update
User::query()->where('id', '=', 1)->update(['name' => 'Updated']);

// Delete
User::query()->where('id', '=', 1)->delete();

// Debug SQL
$dbg = User::query()->where('id', '=', 1)->toSql();
```

Relations with eager loading:

```php
use App\Models\Post;

$posts = Post::query()->with('user')->limit(5)->fetch();
foreach ($posts as $post) {
    echo $post->title . ' by ' . $post->user->name;
}
```

## Migrations & Schema

Generate and run migrations:

```bash
php rolzmaf make:migration create_users_table
php rolzmaf migrate
php rolzmaf migrate:rollback
```

Migration example:

```php
use Core\Schema\Schema;

return new class {
    public function up(): void
    {
        Schema::instance()->create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::instance()->dropIfExists('users');
    }
};
```

## Console Generators

```bash
php rolzmaf make:controller Blog/PostController
php rolzmaf make:model Blog/Post
php rolzmaf make:middleware Admin/VerifyAdmin
```

The generators support nested folders and generate proper namespaces. Models infer table names (snake_case plural) and can restrict columns via `$allowedColumns`.

## Configuration & Security

- Config is loaded from `config/app.php` (via `bootstrap.php`).
- Safe cookie defaults are derived from environment and URL:
  - In `production` or when `APP_URL` is https, cookies are `secure` by default
  - `httponly` true and `samesite` Lax by default
- Override via the `cookie` section in `config/app.php`:

```php
'cookie' => [
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Lax', // Lax | Strict | None
    'path'     => '/',
]
```

## Testing

Run all tests:

```bash
composer test
```

The test suite covers routing, middleware pipeline behavior, HTTP request/response, sessions, schema, migrations, storage and ORM relations. SQLite is used for DB‑related tests.

## Roadmap

- `save()` upsert on models
- Route parameters and groups
- Pagination support
- Validation layer
- Config and route caching
- Additional DB drivers
- Authentication scaffolding

## License

MIT

# Getting Started

## Requirements

- PHP `8.4+`
- Composer
- SQLite (default) or MySQL

## Installation

```bash
git clone https://github.com/ZnarKhalil/rolzmaf-mini-framework.git
cd rolzmaf-mini-framework
composer install
cp .env.example .env

mkdir -p storage/logs storage/files
touch storage/logs/app.log
```

## Environment Setup

Example `.env` for SQLite:

```env
APP_NAME=Rolzmaf
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_DRIVER=sqlite
DB_PATH=storage/database.sqlite
```

Example `.env` for MySQL:

```env
APP_NAME=Rolzmaf
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=framework
DB_USER=root
DB_PASS=
```

## Run HTTP Server

```bash
php -S localhost:8000 -t public
```

Open `http://localhost:8000`.

## Run Migrations

```bash
php rolzmaf migrate
```

## Run Tests

```bash
composer test
```

## First Route Example

`routes/web.php`:

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

## CLI Command Discovery

```bash
php rolzmaf
```

This prints all currently registered commands from `rolzmaf`.

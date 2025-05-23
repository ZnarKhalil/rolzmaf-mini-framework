<?php
declare(strict_types=1);

namespace Tests\Unit\Routing;

use Core\Routing\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Router::class)]
final class RouterTest extends TestCase
{
    #[Test]
    public function it_registers_and_resolves_routes(): void
    {
        $router = new Router();
        $router->get('/', [\App\Controllers\ExampleController::class, 'index'])->middleware();

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';

        $route = $router->resolve(new \Core\Http\Request());

        $this->assertNotNull($route);
        $this->assertSame('/', $route->uri);
        $this->assertSame('GET', $route->method);
        $this->assertSame([\App\Controllers\ExampleController::class, 'index'], $route->action);
    }
}
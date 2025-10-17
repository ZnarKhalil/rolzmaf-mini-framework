<?php

declare(strict_types=1);

namespace Tests\Unit\Routing\Fixtures;

final class RouterControllerStub
{
    public function index(): void
    {
    }
}

namespace Tests\Unit\Routing;

use Core\Http\Request as HttpRequest;
use Core\Routing\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Routing\Fixtures\RouterControllerStub;

#[CoversClass(Router::class)]
final class RouterTest extends TestCase
{
    private array $serverBackup = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverBackup = $_SERVER ?? [];
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
        parent::tearDown();
    }

    #[Test]
    public function it_registers_and_resolves_routes(): void
    {
        $router = new Router();
        $router->get('/', [RouterControllerStub::class, 'index'])->middleware();

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/';

        $route = $router->resolve(new HttpRequest());

        $this->assertNotNull($route);
        $this->assertSame('/', $route->uri);
        $this->assertSame('GET', $route->method);
        $this->assertSame([RouterControllerStub::class, 'index'], $route->action);
    }
}

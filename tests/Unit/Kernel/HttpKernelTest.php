<?php

declare(strict_types=1);

namespace Tests\Unit\Kernel\Fixtures;

use Core\Http\Contracts\RequestInterface;
use Core\Http\Contracts\ResponseInterface;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;

final class GlobalMiddleware implements MiddlewareInterface
{
    public function process(RequestInterface $request, callable $next): ResponseInterface
    {
        $response = $next($request);

        return $response->write(' + global');
    }
}

final class RouteMiddleware implements MiddlewareInterface
{
    public function process(RequestInterface $request, callable $next): ResponseInterface
    {
        $response = $next($request);

        return $response->write(' + route');
    }
}

final class TestController
{
    public function handle(RequestInterface $request): ResponseInterface
    {
        return new Response();
    }
}

final class PlainController
{
    public function plain(): ResponseInterface
    {
        return (new Response())->write('ok');
    }
}

namespace Tests\Unit\Kernel;

use Core\Http\Contracts\ResponseInterface;
use Core\Http\Request;
use Core\Kernel\HttpKernel;
use Core\Routing\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Kernel\Fixtures\GlobalMiddleware;
use Tests\Unit\Kernel\Fixtures\PlainController;
use Tests\Unit\Kernel\Fixtures\RouteMiddleware;
use Tests\Unit\Kernel\Fixtures\TestController;

#[CoversClass(HttpKernel::class)]
final class HttpKernelTest extends TestCase
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
    public function it_executes_middlewares_and_controller(): void
    {
        $router = new Router();
        $router->addGlobalMiddleware(GlobalMiddleware::class);

        $router->get('/test', [TestController::class, 'handle'])->middleware(RouteMiddleware::class);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/test';

        $kernel   = new HttpKernel($router);
        $response = $kernel->handle(new Request());

        $this->assertSame(' + route + global', $this->responseProperty($response, 'content'));
    }

    #[Test]
    public function it_returns_404_for_unknown_route(): void
    {
        $kernel = new HttpKernel(new Router());

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/missing';

        $request = new Request();

        $response = $kernel->handle($request);

        $this->assertSame(404, $this->responseProperty($response, 'status'));
    }

    #[Test]
    public function it_returns_404_for_invalid_controller(): void
    {
        $router = new Router();
        $router->get('/invalid', ['NonExistentController', 'nonExistentMethod']);

        $kernel = new HttpKernel($router);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/invalid';

        $request = new Request();

        $response = $kernel->handle($request);

        $this->assertSame(404, $this->responseProperty($response, 'status'));
    }

    #[Test]
    public function it_invokes_controller_without_request_parameter(): void
    {
        $router = new Router();

        $router->get('/noparams', [PlainController::class, 'plain']);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/noparams';

        $kernel   = new HttpKernel($router);
        $response = $kernel->handle(new Request());

        $this->assertSame('ok', $this->responseProperty($response, 'content'));
    }

    /**
     */
    private function responseProperty(ResponseInterface $response, string $property)
    {
        static $cache = [];
        $class        = $response::class;

        if (!isset($cache[$class][$property])) {
            $ref = new \ReflectionClass($response);
            if (!$ref->hasProperty($property)) {
                throw new \InvalidArgumentException("Property {$property} does not exist on {$class}");
            }

            $prop = $ref->getProperty($property);
            $prop->setAccessible(true);
            $cache[$class][$property] = $prop;
        }

        return $cache[$class][$property]->getValue($response);
    }
}

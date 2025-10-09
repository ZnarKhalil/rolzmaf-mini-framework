<?php

declare(strict_types=1);

namespace Tests\Unit\Kernel;

use Core\Http\Request;
use Core\Kernel\HttpKernel;
use Core\Routing\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(HttpKernel::class)]
final class HttpKernelTest extends TestCase
{
    #[Test]
    public function it_executes_middlewares_and_controller(): void
    {
        $router = new Router();

        // Define middleware classes dynamically
        eval('
            namespace App\Middlewares;
            use Core\Http\Contracts\RequestInterface;
            use Core\Http\Contracts\ResponseInterface;
            use Core\Http\Response;
            use Core\Middleware\MiddlewareInterface;

            class GlobalMiddleware implements MiddlewareInterface {
                public function process(RequestInterface $request, callable $next): ResponseInterface {
                    $res = $next($request);
                    return $res->write(" + global");
                }
            }

            class RouteMiddleware implements MiddlewareInterface {
                public function process(RequestInterface $request, callable $next): ResponseInterface {
                    $res = $next($request);
                    return $res->write(" + route");
                }
            }
        ');

        eval('
            namespace App\Controllers;
            use Core\Http\Contracts\RequestInterface;
            use Core\Http\Contracts\ResponseInterface;
            use Core\Http\Response;

            class TestController {
                public function handle(RequestInterface $request): ResponseInterface {
                    return new Response();
                }
            }
        ');

        $router->addGlobalMiddleware(\App\Middlewares\GlobalMiddleware::class);

        $router->get('/test', [\App\Controllers\TestController::class, 'handle'])->middleware(\App\Middlewares\RouteMiddleware::class);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/test';

        $kernel   = new HttpKernel($router);
        $response = $kernel->handle(new Request());

        $reflection = new \ReflectionClass($response);
        $content    = $reflection->getProperty('content');
        $content->setAccessible(true);

        $this->assertSame(' + route + global', $content->getValue($response));
    }

    #[Test]
    public function it_returns_404_for_unknown_route(): void
    {
        $kernel = new HttpKernel(new Router());

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/missing';

        $request = new Request();

        $response = $kernel->handle($request);

        $reflection = new \ReflectionClass($response);
        $status     = $reflection->getProperty('status');
        $status->setAccessible(true);

        $this->assertSame(404, $status->getValue($response));
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

        $reflection = new \ReflectionClass($response);
        $status     = $reflection->getProperty('status');
        $status->setAccessible(true);

        $this->assertSame(404, $status->getValue($response));
    }

    #[Test]
    public function it_invokes_controller_without_request_parameter(): void
    {
        $router = new Router();

        eval('
            namespace App\\Controllers;
            use Core\\Http\\Response;
            class PlainController { public function plain(): Response { return (new Response())->write("ok"); } }
        ');

        $router->get('/noparams', [\App\Controllers\PlainController::class, 'plain']);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/noparams';

        $kernel   = new HttpKernel($router);
        $response = $kernel->handle(new Request());

        $ref  = new \ReflectionClass($response);
        $prop = $ref->getProperty('content');
        $prop->setAccessible(true);

        $this->assertSame('ok', $prop->getValue($response));
    }
}

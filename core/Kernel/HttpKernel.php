<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Kernel;

use Core\Http\Contracts\RequestInterface;
use Core\Http\Contracts\ResponseInterface;
use Core\Http\Response;
use Core\Middleware\MiddlewarePipeline;
use Core\Routing\Router;

class HttpKernel
{
    protected Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        $route = $this->router->resolve($request);

        if (!$route) {
            return (new Response())->setStatus(404)->write('Not Found');
        }

        $pipeline = new MiddlewarePipeline();

        foreach (array_merge(
            $this->router->globalMiddleware(),
            $route->middleware
        ) as $middlewareClass) {
            $pipeline->add(new $middlewareClass());
        }

        return $pipeline->handle($request, function ($req) use ($route) {
            [$controller, $method] = $route->action;

            return (new $controller())->$method($req);
        });
    }
}

<?php

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
            return new Response()->setStatus(404)->write('Not Found');
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
            if (!class_exists($controller) || !method_exists($controller, $method)) {
                return new Response()->setStatus(404)->write('Controller or method not found');
            }

            $container = \Core\Container\Container::getInstance();

            // Bind the current request instance so it can be injected
            $container->instance(RequestInterface::class, $req);
            $container->instance(\Core\Http\Request::class, $req);

            try {
                $instance = $container->make($controller);

                // We still need to handle method injection for the action method itself
                // The container resolves the constructor dependencies, but not the method call
                // So we'll use the container to resolve method dependencies as well

                $refMethod = new \ReflectionMethod($controller, $method);
                $params = $refMethod->getParameters();
                $args = [];

                foreach ($params as $param) {
                    $type = $param->getType();
                    if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                        $args[] = $container->make($type->getName());
                    } else {
                        // For simple types, we can't easily guess, but if it's a RequestInterface we already bound it.
                        // If it's something else, we might need to rely on route parameters (not implemented yet in this simple router)
                        // For now, we'll just leave it null or default
                        if ($param->isDefaultValueAvailable()) {
                            $args[] = $param->getDefaultValue();
                        } else {
                            $args[] = null;
                        }
                    }
                }

                return $refMethod->invokeArgs($instance, $args);

            } catch (\Exception $e) {
                // In production, log this error
                return new Response()->setStatus(500)->write('Failed to invoke controller: ' . $e->getMessage());
            }
        });
    }
}

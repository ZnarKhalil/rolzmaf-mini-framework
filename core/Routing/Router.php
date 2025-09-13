<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\Http\Contracts\RequestInterface;

class Router
{
    private array $routes           = [];
    private array $globalMiddleware = [];

    public function addGlobalMiddleware(string $middleware): void
    {
        $this->globalMiddleware[] = $middleware;
    }

    public function get(string $uri, array $action): RouteBuilder
    {
        return new RouteBuilder('GET', $uri, $action, $this);
    }

    public function post(string $uri, array $action): RouteBuilder
    {
        return new RouteBuilder('POST', $uri, $action, $this);
    }

    public function put(string $uri, array $action): RouteBuilder
    {
        return new RouteBuilder('PUT', $uri, $action, $this);
    }

    public function delete(string $uri, array $action): RouteBuilder
    {
        return new RouteBuilder('DELETE', $uri, $action, $this);
    }

    public function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function resolve(RequestInterface $request): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->method === $request->method() && $route->uri === $request->uri()) {
                return $route;
            }
        }

        return null;
    }

    public function globalMiddleware(): array
    {
        return $this->globalMiddleware;
    }
}

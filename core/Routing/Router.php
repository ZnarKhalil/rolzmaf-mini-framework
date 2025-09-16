<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\Http\Contracts\RequestInterface;

class Router
{
    private array $routes = [];
    private array $globalMiddleware = [];

    public function addGlobalMiddleware(string $middleware): void
    {
        $this->globalMiddleware[] = $middleware;
    }

    public function get(string $uri, array $action): Route
    {
        $route = new Route('GET', $uri, $action);
        $this->routes[] = $route;
        return $route;
    }

    public function post(string $uri, array $action): Route
    {
        $route = new Route('POST', $uri, $action);
        $this->routes[] = $route;
        return $route;
    }

    public function put(string $uri, array $action): Route
    {
        $route = new Route('PUT', $uri, $action);
        $this->routes[] = $route;
        return $route;
    }

    public function delete(string $uri, array $action): Route
    {
        $route = new Route('DELETE', $uri, $action);
        $this->routes[] = $route;
        return $route;
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
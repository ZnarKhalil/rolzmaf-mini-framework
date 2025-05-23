<?php
namespace Core\Routing;

class RouteBuilder
{
    public function __construct(
        private string $method,
        private string $uri,
        private array $action,
        private Router $router
    ) {}

    public function middleware(string ...$middleware): void
    {
        $route = new Route($this->method, $this->uri, $this->action, $middleware);
        $this->router->addRoute($route);
    }
}
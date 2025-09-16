<?php

declare(strict_types=1);

namespace Core\Routing;

class Route
{
    public array $middleware = [] {
        get {
            return $this->middleware;
        }
    }

    public function __construct(
        public readonly string $method,
        public readonly string $uri,
        public readonly array $action,
        array $middleware = []
    ) {
        $this->middleware = $middleware;
    }

    public function middleware(string ...$middleware): self
    {
        $this->middleware = array_merge($this->middleware, $middleware);

        return $this;
    }

}

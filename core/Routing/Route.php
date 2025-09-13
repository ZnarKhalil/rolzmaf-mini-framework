<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Routing;

class Route
{
    public function __construct(
        public readonly string $method,
        public readonly string $uri,
        public readonly array $action, // [Controller::class, 'method']
        public readonly array $middleware = []
    ) {
    }
}

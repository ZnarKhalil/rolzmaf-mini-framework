<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Middleware;

use Core\Http\Contracts\RequestInterface;
use Core\Http\Contracts\ResponseInterface;

interface MiddlewareInterface
{
    public function process(RequestInterface $request, callable $next): ResponseInterface;
}

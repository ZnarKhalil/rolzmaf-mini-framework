<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Middleware;

use Core\Http\Contracts\RequestInterface;
use Core\Http\Contracts\ResponseInterface;

class MiddlewarePipeline
{
    /**
     * @var MiddlewareInterface[]
     */
    protected array $middleware = [];

    public function add(MiddlewareInterface $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    public function handle(RequestInterface $request, callable $coreHandler): ResponseInterface
    {
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            fn (callable $next, MiddlewareInterface $middleware) => fn (RequestInterface $req) => $middleware->process($req, $next),
            $coreHandler
        );

        return $pipeline($request);
    }
}

<?php


declare(strict_types=1);

namespace Core\Middleware\Examples;

use Core\Http\Contracts\RequestInterface;
use Core\Http\Contracts\ResponseInterface;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;

class ExampleMiddleware implements MiddlewareInterface
{
    public function process(RequestInterface $request, callable $next): ResponseInterface
    {
        if ($request->header('x-block-me') === 'yes') {
            return (new Response())
                ->setStatus(403)
                ->write('Access Denied');
        }

        return $next($request);
    }
}

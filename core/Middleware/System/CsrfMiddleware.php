<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Core\Middleware\System;

use Core\Http\Contracts\RequestInterface;
use Core\Http\Contracts\ResponseInterface;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;
use Core\Session\Session;

class CsrfMiddleware implements MiddlewareInterface
{
    public function process(RequestInterface $request, callable $next): ResponseInterface
    {
        Session::start();

        $method = $request->method();

        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            return $next($request);
        }

        $token = Session::get('_csrf_token');

        $userToken = $request->input('_token') ?? $request->header('x-csrf-token');

        if (!$token || !$userToken || !hash_equals($token, $userToken)) {
            return (new Response())->setStatus(403)->write('CSRF token mismatch');
        }

        return $next($request);
    }

    public static function token(): string
    {
        Session::start();

        $token = Session::get('_csrf_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set('_csrf_token', $token);
        }

        return $token;
    }
}

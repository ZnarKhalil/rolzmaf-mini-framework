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

            $instance = new $controller();

            try {
                $refMethod = \ReflectionMethod::createFromMethodName("{$controller}::{$method}");
                $params    = $refMethod->getParameters();

                if (count($params) === 0) {
                    return $instance->$method();
                }

                $param = $params[0];
                $type  = $param->getType();

                if ($type instanceof \ReflectionNamedType
                    && !$type->isBuiltin()
                    && is_a($type->getName(), RequestInterface::class, true)
                ) {
                    return $instance->$method($req);
                }

                // Fallback: call without arguments
                return $instance->$method();
            } catch (\ReflectionException $e) {
                return new Response()->setStatus(500)->write('Failed to invoke controller');
            }
        });
    }
}

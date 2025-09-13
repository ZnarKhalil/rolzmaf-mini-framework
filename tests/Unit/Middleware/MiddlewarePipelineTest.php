<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;
use Core\Middleware\MiddlewarePipeline;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MiddlewarePipeline::class)]
final class MiddlewarePipelineTest extends TestCase
{
    #[Test]
    public function it_passes_through_all_middleware(): void
    {
        $pipeline = new MiddlewarePipeline();

        $pipeline->add(new class () implements MiddlewareInterface {
            public function process(\Core\Http\Contracts\RequestInterface $request, callable $next): \Core\Http\Contracts\ResponseInterface
            {
                $response = $next($request);

                return $response->write(' + mw1');
            }
        });

        $pipeline->add(new class () implements MiddlewareInterface {
            public function process(\Core\Http\Contracts\RequestInterface $request, callable $next): \Core\Http\Contracts\ResponseInterface
            {
                $response = $next($request);

                return $response->write(' + mw2');
            }
        });

        $request  = new Request();
        $response = $pipeline->handle($request, fn ($r) => (new Response())->write('core'));

        $reflection = new \ReflectionClass($response);
        $content    = $reflection->getProperty('content');
        $content->setAccessible(true);

        $this->assertSame('core + mw2 + mw1', $content->getValue($response));
    }
}

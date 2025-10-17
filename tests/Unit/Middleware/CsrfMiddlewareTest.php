<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\System\CsrfMiddleware;
use Core\Session\Session;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CsrfMiddleware::class)]
final class CsrfMiddlewareTest extends TestCase
{
    private array $serverBackup = [];
    private array $postBackup   = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverBackup = $_SERVER ?? [];
        $this->postBackup   = $_POST   ?? [];
        Session::destroy();
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
        $_POST   = $this->postBackup;
        Session::destroy();
        parent::tearDown();
    }

    #[Test]
    public function allows_get_requests_without_token(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $middleware                = new CsrfMiddleware();

        $result = $middleware->process(new Request(), fn () => new Response());
        $this->assertInstanceOf(Response::class, $result);
    }

    #[Test]
    public function blocks_post_with_missing_token(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST                     = [];

        $middleware = new CsrfMiddleware();
        $response   = $middleware->process(new Request(), fn () => new Response());

        $reflection = new \ReflectionClass($response);
        $status     = $reflection->getProperty('status');
        $status->setAccessible(true);

        $this->assertSame(403, $status->getValue($response));
    }

    #[Test]
    public function passes_when_token_matches(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $token                     = CsrfMiddleware::token();
        $_POST['_token']           = $token;

        $middleware = new CsrfMiddleware();
        $result     = $middleware->process(new Request(), fn () => new Response());

        $this->assertInstanceOf(Response::class, $result);
    }
}

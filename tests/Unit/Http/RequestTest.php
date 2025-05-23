<?php
declare(strict_types=1);

namespace Tests\Http;

use Core\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Request::class)]
final class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test/uri?x=1';
        $_GET = ['x' => '1'];
        $_POST = ['name' => 'Test'];
        $_SERVER['HTTP_X_CUSTOM'] = 'ABC';
    }

    #[Test]
    public function it_parses_method_and_uri(): void
    {
        $request = new Request();
        $this->assertSame('POST', $request->method());
        $this->assertSame('/test/uri', $request->uri());
    }

    #[Test]
    public function it_reads_query_and_input(): void
    {
        $request = new Request();
        $this->assertSame('1', $request->query('x'));
        $this->assertSame('Test', $request->input('name'));
    }

    #[Test]
    public function it_reads_headers(): void
    {
        $request = new Request();
        $this->assertSame('ABC', $request->header('x-custom'));
    }
}
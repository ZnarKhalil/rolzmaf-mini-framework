<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Fixtures;

final class PhpInputStub
{
    public static ?string $content = null;
}

namespace Core\Http;

use Tests\Unit\Http\Fixtures\PhpInputStub;

if (!function_exists(__NAMESPACE__ . '\\file_get_contents')) {
    /**
     * @param int $length Unused but kept for signature parity via optional argument
     */
    function file_get_contents(string $filename, bool $useIncludePath = false, $context = null, int $offset = 0, ?int $length = null): string|false
    {
        if ($filename === 'php://input' && PhpInputStub::$content !== null) {
            return PhpInputStub::$content;
        }

        return \call_user_func_array('\file_get_contents', func_get_args());
    }
}

namespace Tests\Unit\Http;

use Core\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Http\Fixtures\PhpInputStub;

#[CoversClass(Request::class)]
#[TestDox('Http Request')]
final class RequestTest extends TestCase
{
    private array $serverBackup = [];
    private array $getBackup    = [];
    private array $postBackup   = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->serverBackup = $_SERVER ?? [];
        $this->getBackup    = $_GET    ?? [];
        $this->postBackup   = $_POST   ?? [];

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI']    = '/test/uri?x=1';
        $_GET                      = ['x' => '1'];
        $_POST                     = ['name' => 'Test'];
        $_SERVER['HTTP_X_CUSTOM']  = 'ABC';
        PhpInputStub::$content     = null;
    }

    protected function tearDown(): void
    {
        $_SERVER               = $this->serverBackup;
        $_GET                  = $this->getBackup;
        $_POST                 = $this->postBackup;
        PhpInputStub::$content = null;

        parent::tearDown();
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

    #[Test]
    public function it_returns_empty_json_when_content_type_is_not_json(): void
    {
        unset($_SERVER['HTTP_CONTENT_TYPE']);
        PhpInputStub::$content = '{"ignored":true}';

        $request = new Request();
        $this->assertSame([], $request->json());
        $this->assertNull($request->jsonError());
    }

    #[Test]
    public function it_parses_valid_json_and_has_no_error(): void
    {
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        PhpInputStub::$content        = '{"a":1,"b":"x"}';

        $request = new Request();

        $this->assertSame(['a' => 1, 'b' => 'x'], $request->json());
        $this->assertNull($request->jsonError());
    }

    #[Test]
    public function it_sets_json_error_on_invalid_body(): void
    {
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        PhpInputStub::$content        = '{"a":';

        $request = new Request();

        $this->assertSame([], $request->json());
        $this->assertNotNull($request->jsonError());
    }
}

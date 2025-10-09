<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use Core\Http\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Response::class)]
#[TestDox('Http Response')]
final class ResponseTest extends TestCase
{
    #[Test]
    public function it_sets_status_header_and_content(): void
    {
        $response = new Response();

        $response->setStatus(404)
                 ->setHeader('Content-Type', 'text/plain')
                 ->write('Not Found');

        $reflection = new \ReflectionClass($response);

        $status = $reflection->getProperty('status');
        $status->setAccessible(true);
        $this->assertSame(404, $status->getValue($response));

        $headers = $reflection->getProperty('headers');
        $headers->setAccessible(true);
        $this->assertSame(['Content-Type' => 'text/plain'], $headers->getValue($response));

        $content = $reflection->getProperty('content');
        $content->setAccessible(true);
        $this->assertSame('Not Found', $content->getValue($response));
    }

    #[Test]
    public function it_builds_json_response(): void
    {
        $response = (new Response())->json(['ok' => true, 'n' => 1], 201);

        $ref    = new \ReflectionClass($response);
        $status = $ref->getProperty('status');
        $status->setAccessible(true);
        $headers = $ref->getProperty('headers');
        $headers->setAccessible(true);
        $content = $ref->getProperty('content');
        $content->setAccessible(true);

        $this->assertSame(201, $status->getValue($response));
        $this->assertSame('application/json; charset=utf-8', $headers->getValue($response)['Content-Type'] ?? null);
        $this->assertSame('{"ok":true,"n":1}', $content->getValue($response));
    }

    #[Test]
    public function it_builds_redirect_response(): void
    {
        $response = (new Response())->redirect('/login', 302);

        $ref    = new \ReflectionClass($response);
        $status = $ref->getProperty('status');
        $status->setAccessible(true);
        $headers = $ref->getProperty('headers');
        $headers->setAccessible(true);

        $this->assertSame(302, $status->getValue($response));
        $this->assertSame('/login', $headers->getValue($response)['Location'] ?? null);
    }
}

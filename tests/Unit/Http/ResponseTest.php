<?php


declare(strict_types=1);

namespace Tests\Unit\Http;

use Core\Http\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Response::class)]
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
}

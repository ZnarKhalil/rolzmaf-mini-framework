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

        $this->assertSame(404, $this->responseProperty($response, 'status'));
        $this->assertSame(['Content-Type' => 'text/plain'], $this->responseProperty($response, 'headers'));
        $this->assertSame('Not Found', $this->responseProperty($response, 'content'));
    }

    #[Test]
    public function it_builds_json_response(): void
    {
        $response = (new Response())->json(['ok' => true, 'n' => 1], 201);

        $headers = $this->responseProperty($response, 'headers');

        $this->assertSame(201, $this->responseProperty($response, 'status'));
        $this->assertSame('application/json; charset=utf-8', $headers['Content-Type'] ?? null);
        $this->assertSame('{"ok":true,"n":1}', $this->responseProperty($response, 'content'));
    }

    #[Test]
    public function it_builds_redirect_response(): void
    {
        $response = (new Response())->redirect('/login', 302);

        $headers = $this->responseProperty($response, 'headers');

        $this->assertSame(302, $this->responseProperty($response, 'status'));
        $this->assertSame('/login', $headers['Location'] ?? null);
    }

    /**
     */
    private function responseProperty(Response $response, string $property)
    {
        static $cache = [];
        $class        = $response::class;

        if (!isset($cache[$class][$property])) {
            $ref = new \ReflectionClass($response);
            if (!$ref->hasProperty($property)) {
                throw new \InvalidArgumentException("Property {$property} does not exist on {$class}");
            }

            $prop = $ref->getProperty($property);
            $prop->setAccessible(true);
            $cache[$class][$property] = $prop;
        }

        return $cache[$class][$property]->getValue($response);
    }
}

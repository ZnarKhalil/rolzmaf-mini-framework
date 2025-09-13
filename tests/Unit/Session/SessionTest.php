<?php


declare(strict_types=1);

namespace Tests\Unit\Session;

use Core\Session\Session;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Session::class)]
final class SessionTest extends TestCase
{
    #[Test]
    public function it_sets_and_gets_session_values(): void
    {
        Session::set('foo', 'bar');
        $this->assertSame('bar', Session::get('foo'));
    }

    #[Test]
    public function it_checks_and_removes_keys(): void
    {
        Session::set('foo', 'bar');
        $this->assertTrue(Session::has('foo'));
        Session::remove('foo');
        $this->assertFalse(Session::has('foo'));
    }
}

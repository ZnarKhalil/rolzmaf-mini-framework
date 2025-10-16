<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use Core\Http\Cookie;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Cookie::class)]
final class CookieTest extends TestCase
{
    private array $cookieBackup = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->cookieBackup = $_COOKIE ?? [];
    }

    protected function tearDown(): void
    {
        $_COOKIE = $this->cookieBackup;
        parent::tearDown();
    }

    #[Test]
    public function it_sets_and_reads_cookie(): void
    {
        $_COOKIE['theme'] = 'dark';
        $this->assertSame('dark', Cookie::get('theme'));
    }

    #[Test]
    public function it_deletes_cookie(): void
    {
        $_COOKIE['temp'] = '123';
        Cookie::delete('temp');
        $this->assertArrayNotHasKey('temp', $_COOKIE);
    }
}

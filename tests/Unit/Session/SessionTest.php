<?php

declare(strict_types=1);

namespace Tests\Unit\Session;

use Core\Config\Config;
use Core\Session\Session;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Session::class)]
#[TestDox('Session management')]
final class SessionTest extends TestCase
{
    protected function tearDown(): void
    {
        Session::destroy();
        parent::tearDown();
    }

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

    #[Test]
    #[RunInSeparateProcess]
    public function it_applies_secure_cookie_params_from_config_and_env(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // Simulate production + https URL
        $_ENV['APP_ENV'] = 'production';
        $_ENV['APP_URL'] = 'https://example.test';

        Config::load(__DIR__ . '/../../../config/app.php');

        Session::start();

        $params = session_get_cookie_params();

        $this->assertTrue((bool) ($params['secure'] ?? false));
        $this->assertTrue((bool) ($params['httponly'] ?? false));
        $this->assertSame('/', $params['path'] ?? null);
        $this->assertTrue(in_array($params['samesite'] ?? 'Lax', ['Lax', 'Strict', 'None'], true));

        // Cleanup
        session_write_close();
    }
}

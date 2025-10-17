<?php

declare(strict_types=1);

namespace Tests\Unit\Session;

use Core\Session\Flash;
use Core\Session\Session;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Flash::class)]
final class FlashTest extends TestCase
{
    protected function tearDown(): void
    {
        Session::destroy();
        parent::tearDown();
    }

    #[Test]
    public function it_sets_and_gets_flash_once(): void
    {
        Flash::set('msg', 'Hello');
        $this->assertSame('Hello', Flash::get('msg'));
        $this->assertNull(Flash::get('msg')); // gone after access
    }

    #[Test]
    public function it_returns_all_and_clears(): void
    {
        Flash::set('a', 1);
        Flash::set('b', 2);
        $this->assertSame(['a' => 1, 'b' => 2], Flash::all());
        $this->assertNull(Flash::get('a'));
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Schema;

use Core\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Schema::class)]
final class SchemaTest extends TestCase
{
    protected function tearDown(): void
    {
        Schema::reset();
    }

    #[Test]
    public function it_returns_singleton_instance(): void
    {
        $instance1 = Schema::instance();
        $instance2 = Schema::instance();

        $this->assertSame($instance1, $instance2);
    }
}

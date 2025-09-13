<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace Tests\Unit\Schema;

use Core\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Schema::class)]
final class SchemaTest extends TestCase
{
    protected function tearDown(): void
    {
        Schema::reset();
    }

    #[Test]
    public function test_schema_instance_returns_singleton(): void
    {
        $instance1 = Schema::instance();
        $instance2 = Schema::instance();

        $this->assertSame($instance1, $instance2);
    }
}

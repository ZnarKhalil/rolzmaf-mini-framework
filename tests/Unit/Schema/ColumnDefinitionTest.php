<?php

declare(strict_types=1);

namespace Tests\Unit\Schema;

use Core\Schema\ColumnDefinition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ColumnDefinition::class)]
final class ColumnDefinitionTest extends TestCase
{
    #[Test]
    public function test_generates_basic_sql(): void
    {
        $column = new ColumnDefinition('name', 'VARCHAR', 255);
        $sql    = $column->toSql();

        $this->assertStringContainsString('`name` VARCHAR(255)', $sql);
    }

    #[Test]
    public function test_applies_nullable_and_unique(): void
    {
        $column = new ColumnDefinition('email', 'VARCHAR', 255);
        $column->nullable();
        $column->unique();
        $sql = $column->toSql();

        $this->assertStringContainsString('NULL', $sql);
        $this->assertStringContainsString('UNIQUE', $sql);
    }
}
